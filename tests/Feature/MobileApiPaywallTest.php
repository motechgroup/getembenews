<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileApiPaywallTest extends TestCase
{
    use RefreshDatabase;

    public function test_mobile_api_article_returns_paywall_metadata_and_teaser_for_locked_premium_article()
    {
        $category = Category::create([
            'name' => 'Politics',
            'slug' => 'politics',
            'order' => 1,
        ]);

        $author = User::create([
            'name' => 'Test Author',
            'email' => 'author@example.com',
            'password' => bcrypt('password'),
            'role' => 'editor',
        ]);

        $article = Article::create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Exclusive Premium News Story',
            'slug' => 'exclusive-premium-news-story',
            'body' => '<p>First paragraph snippet of premium news story.</p><p>Second paragraph with confidential details.</p><p>Third paragraph with complete breakdown.</p>',
            'status' => 'published',
            'published_at' => now()->subDay(),
            'is_premium' => true,
            'price' => 50,
        ]);

        $response = $this->getJson("/api/v1/articles/{$article->slug}");

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'data' => [
                'paywall' => [
                    'is_premium' => true,
                    'is_locked' => true,
                    'user_has_access' => false,
                    'article_price' => 50,
                ]
            ]
        ]);

        // Body should be truncated to paragraph 1 teaser
        $this->assertStringContainsString('First paragraph snippet', $response->json('data.article.body'));
        $this->assertStringNotContainsString('Second paragraph with confidential details', $response->json('data.article.body'));
    }

    public function test_authenticated_mobile_user_with_subscription_gets_full_article_body()
    {
        $category = Category::create([
            'name' => 'Business',
            'slug' => 'business',
            'order' => 2,
        ]);

        $author = User::create([
            'name' => 'Test Author 2',
            'email' => 'author2@example.com',
            'password' => bcrypt('password'),
            'role' => 'editor',
        ]);

        $user = User::create([
            'name' => 'Subscribed Reader',
            'email' => 'subscriber@example.com',
            'password' => bcrypt('password'),
            'role' => 'subscriber',
            'subscription_plan' => 'monthly',
            'subscription_expires_at' => now()->addMonth(),
        ]);

        $article = Article::create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Exclusive Business Analysis',
            'slug' => 'exclusive-business-analysis',
            'body' => '<p>First paragraph snippet of premium news story.</p><p>Second paragraph with confidential details.</p>',
            'status' => 'published',
            'published_at' => now()->subDay(),
            'is_premium' => true,
            'price' => 50,
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/v1/articles/{$article->slug}");

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'data' => [
                'paywall' => [
                    'is_premium' => true,
                    'is_locked' => false,
                    'user_has_access' => true,
                ]
            ]
        ]);

        $this->assertStringContainsString('Second paragraph with confidential details', $response->json('data.article.body'));
    }
}
