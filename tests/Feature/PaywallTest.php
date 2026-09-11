<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ArticlePurchase;
use App\Models\ArticleSubscription;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class PaywallTest extends TestCase
{
    use RefreshDatabase;

    private function createArticle(array $attributes = []): Article
    {
        $author = User::factory()->create(['role' => 'editor']);
        $category = Category::firstOrCreate(['slug' => 'news'], [
            'name' => 'News',
            'order' => 1,
        ]);
        $title = $attributes['title'] ?? 'Test Article Title ' . uniqid();
        return Article::create(array_merge([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'title' => $title,
            'slug' => Str::slug($title),
            'seo_description' => 'Summary excerpt of the story for SEO metadata',
            'body' => '<p>First paragraph of the article body text that is publicly accessible as teaser.</p><p>Second paragraph of the article body content with details.</p><p>Third paragraph that contains secret premium information.</p>',
            'status' => 'published',
            'published_at' => now(),
            'is_premium' => false,
            'price' => 50,
        ], $attributes));
    }

    public function test_free_article_can_be_viewed_by_anyone(): void
    {
        $article = $this->createArticle([
            'title' => 'Free Public News Article',
            'is_premium' => false,
        ]);

        $response = $this->get('/articles/' . $article->slug);
        $response->assertOk();
        $response->assertSee($article->title);
        $response->assertDontSee('Paywall Access Locked');
    }

    public function test_premium_article_shows_paywall_lock_for_guests(): void
    {
        $article = $this->createArticle([
            'title' => 'Exclusive Premium Analysis Article',
            'is_premium' => true,
            'price' => 50,
        ]);

        $response = $this->get('/articles/' . $article->slug);
        $response->assertOk();
        $response->assertSee('Exclusive Premium Analysis Article');
        $response->assertSee('PREMIUM ARTICLE');
        $response->assertSee('Unlock Full Article Access');
        $response->assertSee('First paragraph of the article body text that is publicly accessible as teaser.');
        $response->assertDontSee('Second paragraph of the article body content with details.');
        $response->assertDontSee('Sign In Required to Pay');
    }

    public function test_guest_initiate_payment_redirects_to_register(): void
    {
        $article = $this->createArticle([
            'title' => 'Guest Redirect Test Article',
            'is_premium' => true,
            'price' => 50,
        ]);

        Livewire::test(\App\Livewire\ArticlePaywallModal::class, ['article' => $article])
            ->call('initiatePayment')
            ->assertRedirect(route('register'));

        Livewire::test(\App\Livewire\ArticlePaywallModal::class, ['article' => $article])
            ->call('selectOption', 'daily')
            ->assertRedirect(route('register'));
    }

    public function test_logged_in_user_opens_payment_modal_popup(): void
    {
        $user = User::factory()->create(['role' => 'subscriber']);
        $article = $this->createArticle([
            'title' => 'Logged In User Modal Test Article',
            'is_premium' => true,
            'price' => 50,
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\ArticlePaywallModal::class, ['article' => $article])
            ->assertSet('showPaymentModal', false)
            ->call('openPaymentModal')
            ->assertSet('showPaymentModal', true)
            ->call('closePaymentModal')
            ->assertSet('showPaymentModal', false);
    }

    public function test_staff_users_automatically_bypass_premium_paywall(): void
    {
        $editor = User::factory()->create(['role' => 'editor']);
        $article = $this->createArticle([
            'title' => 'Exclusive Premium Analysis Article Staff Test',
            'is_premium' => true,
            'price' => 50,
        ]);

        $response = $this->actingAs($editor)->get('/articles/' . $article->slug);
        $response->assertOk();
        $response->assertDontSee('Unlock Full Story');
    }

    public function test_user_with_active_subscription_bypasses_paywall(): void
    {
        $subscriber = User::factory()->create([
            'role' => 'subscriber',
            'subscription_plan' => 'daily',
            'subscription_expires_at' => now()->addHours(12),
        ]);

        $article = $this->createArticle([
            'title' => 'Premium Article Subscribed User Test',
            'is_premium' => true,
            'price' => 50,
        ]);

        $this->assertTrue($subscriber->hasActiveSubscription());
        $this->assertTrue($subscriber->canAccessArticle($article));

        $response = $this->actingAs($subscriber)->get('/articles/' . $article->slug);
        $response->assertOk();
        $response->assertDontSee('Unlock Full Story');
    }

    public function test_user_who_purchased_article_bypasses_paywall(): void
    {
        $user = User::factory()->create(['role' => 'subscriber']);
        $article = $this->createArticle([
            'title' => 'Purchased Article Access Test',
            'is_premium' => true,
            'price' => 50,
        ]);

        ArticlePurchase::create([
            'user_id' => $user->id,
            'article_id' => $article->id,
            'amount' => 50,
            'phone_number' => '254712345678',
            'mpesa_reference' => 'TESTMPESA123',
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        $this->assertTrue($user->hasPurchasedArticle($article));
        $this->assertTrue($user->canAccessArticle($article));

        $response = $this->actingAs($user)->get('/articles/' . $article->slug);
        $response->assertOk();
        $response->assertDontSee('Unlock Full Story');
    }

    public function test_mpesa_callback_fulfills_article_purchase(): void
    {
        $user = User::factory()->create(['role' => 'subscriber']);
        $article = $this->createArticle([
            'title' => 'Article Callback Purchase Test',
            'is_premium' => true,
            'price' => 50,
        ]);

        $checkoutReqId = 'ws_CO_TEST_' . uniqid();
        Cache::put('mpesa_paywall_' . $checkoutReqId, [
            'user_id' => $user->id,
            'article_id' => $article->id,
            'option' => 'single',
            'amount' => 50,
            'phone' => '254712345678',
        ], 3600);

        $payload = [
            'Body' => [
                'stkCallback' => [
                    'MerchantRequestID' => 'MERCHANT_123',
                    'CheckoutRequestID' => $checkoutReqId,
                    'ResultCode' => 0,
                    'ResultDesc' => 'The service request is processed successfully.',
                    'CallbackMetadata' => [
                        'Item' => [
                            ['Name' => 'Amount', 'Value' => 50],
                            ['Name' => 'MpesaReceiptNumber', 'Value' => 'RCK1234567'],
                            ['Name' => 'TransactionDate', 'Value' => 20260911120000],
                            ['Name' => 'PhoneNumber', 'Value' => 254712345678],
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->postJson('/api/v1/payments/mpesa/callback', $payload);
        $response->assertOk();
        $response->assertJson(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);

        $this->assertDatabaseHas('article_purchases', [
            'user_id' => $user->id,
            'article_id' => $article->id,
            'mpesa_reference' => 'RCK1234567',
            'status' => 'completed',
        ]);

        $this->assertTrue($user->fresh()->hasPurchasedArticle($article));
    }

    public function test_mpesa_callback_fulfills_subscription_pass(): void
    {
        $user = User::factory()->create(['role' => 'subscriber']);

        $checkoutReqId = 'ws_CO_SUB_' . uniqid();
        Cache::put('mpesa_paywall_' . $checkoutReqId, [
            'user_id' => $user->id,
            'article_id' => null,
            'option' => 'monthly',
            'amount' => 300,
            'phone' => '254712345678',
        ], 3600);

        $payload = [
            'Body' => [
                'stkCallback' => [
                    'MerchantRequestID' => 'MERCHANT_123',
                    'CheckoutRequestID' => $checkoutReqId,
                    'ResultCode' => 0,
                    'ResultDesc' => 'The service request is processed successfully.',
                    'CallbackMetadata' => [
                        'Item' => [
                            ['Name' => 'Amount', 'Value' => 300],
                            ['Name' => 'MpesaReceiptNumber', 'Value' => 'SUB99988877'],
                            ['Name' => 'TransactionDate', 'Value' => 20260911120000],
                            ['Name' => 'PhoneNumber', 'Value' => 254712345678],
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->postJson('/api/v1/payments/mpesa/callback', $payload);
        $response->assertOk();
        $response->assertJson(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);

        $this->assertDatabaseHas('article_subscriptions', [
            'user_id' => $user->id,
            'plan' => 'monthly',
            'mpesa_reference' => 'SUB99988877',
            'status' => 'active',
        ]);

        $this->assertTrue($user->fresh()->hasActiveSubscription());
    }

    public function test_user_dashboard_displays_paid_articles_and_transaction_logs(): void
    {
        $user = User::factory()->create([
            'role' => 'subscriber',
            'email_verified_at' => now(),
            'subscription_plan' => 'weekly',
            'subscription_expires_at' => now()->addDays(7),
        ]);

        $article = $this->createArticle([
            'title' => 'Dashboard Ledger Unlocked Story',
            'is_premium' => true,
        ]);

        ArticlePurchase::create([
            'user_id' => $user->id,
            'article_id' => $article->id,
            'amount' => 10,
            'phone_number' => '254712345678',
            'mpesa_reference' => 'DASHBOARDMPESA123',
            'status' => 'completed',
        ]);

        ArticleSubscription::create([
            'user_id' => $user->id,
            'plan' => 'weekly',
            'amount' => 50,
            'phone_number' => '254712345678',
            'starts_at' => now(),
            'expires_at' => now()->addDays(7),
            'mpesa_reference' => 'DASHBOARDSUB456',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertOk();
        $response->assertSee('My Subscription & Article Pass Status', false);
        $response->assertSee('Active Weekly Pass');
        $response->assertSee('My Unlocked Premium Articles');
        $response->assertSee('Dashboard Ledger Unlocked Story');
        $response->assertSee('M-Pesa Payment & Subscription Logs', false);
        $response->assertSee('DASHBOARDMPESA123');
        $response->assertSee('DASHBOARDSUB456');
    }
}
