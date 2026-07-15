<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Announcement;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AnnouncementTest extends TestCase
{
    use RefreshDatabase;

    public function test_announcement_page_can_be_rendered(): void
    {
        $response = $this->get('/announcements');
        $response->assertOk();
    }

    public function test_announcement_submit_flow(): void
    {
        Setting::set('announcement_rate_tv', '5');

        $component = Livewire::test(\App\Livewire\AnnouncementSubmit::class)
            ->set('visitor_name', 'Emma Nyabera')
            ->set('visitor_email', '') // Optional email
            ->set('visitor_phone', '+254712345678')
            ->set('type', 'funeral')
            ->set('media', 'tv')
            ->set('days_count', 3)
            ->set('content', 'This is a test funeral announcement containing exactly eight words here.');

        // Word count is 11
        $this->assertEquals(11, $component->get('word_count'));
        // Rate is 5
        $this->assertEquals(5, $component->get('rate'));
        // Total price is 11 words * 5 KSH * 3 days = 165
        $this->assertEquals(165, $component->get('total_price'));

        $component->call('submitAnnouncement');

        $component->assertSet('showCheckoutModal', true);
        $this->assertDatabaseHas('announcements', [
            'visitor_name' => 'Emma Nyabera',
            'visitor_email' => null,
            'payment_status' => 'pending',
        ]);

        $announcementId = $component->get('currentAnnouncementId');
        $ann = Announcement::findOrFail($announcementId);
        $this->assertEquals(now()->toDateString(), $ann->airing_date->toDateString());
        $this->assertEquals(now()->addDays(3)->toDateString(), $ann->expiry_date->toDateString());

        // Verify system draft alert was logged to contact messages
        $this->assertDatabaseHas('contact_messages', [
            'name' => 'System Alert',
            'subject' => 'New Announcement Submitted (Pending Payment)',
        ]);

        $component->call('triggerMpesaStkPush');
        $component->assertSet('mpesa_status', 'sending');

        $component->call('confirmPaymentSuccess');
        $component->assertSet('mpesa_status', 'success');

        $this->assertDatabaseHas('announcements', [
            'id' => $announcementId,
            'payment_status' => 'paid',
        ]);

        // Verify system paid alert was logged to contact messages
        $this->assertDatabaseHas('contact_messages', [
            'name' => 'System Alert',
            'subject' => 'Announcement Paid (Ref: ' . Announcement::find($announcementId)->payment_reference . ')',
        ]);
    }

    public function test_active_announcements_scope(): void
    {
        // 1. Airing today, 2 days count (expires in 2 days) -> active
        $activeAnn = Announcement::create([
            'visitor_name' => 'Active Sub',
            'visitor_phone' => '123456',
            'type' => 'general',
            'media' => 'tv',
            'content' => 'This is active.',
            'word_count' => 3,
            'days_count' => 2,
            'rate_per_word' => 5,
            'total_amount' => 30,
            'payment_status' => 'paid',
            'is_approved' => true,
            'airing_date' => now()->toDateString(),
        ]);

        // 2. Airing tomorrow -> not active today
        $futureAnn = Announcement::create([
            'visitor_name' => 'Future Sub',
            'visitor_phone' => '123456',
            'type' => 'general',
            'media' => 'tv',
            'content' => 'This is future.',
            'word_count' => 3,
            'days_count' => 2,
            'rate_per_word' => 5,
            'total_amount' => 30,
            'payment_status' => 'paid',
            'is_approved' => true,
            'airing_date' => now()->addDay()->toDateString(),
        ]);

        // 3. Airing yesterday for 1 day (expired today) -> not active
        $expiredAnn = Announcement::create([
            'visitor_name' => 'Expired Sub',
            'visitor_phone' => '123456',
            'type' => 'general',
            'media' => 'tv',
            'content' => 'This is expired.',
            'word_count' => 3,
            'days_count' => 1,
            'rate_per_word' => 5,
            'total_amount' => 15,
            'payment_status' => 'paid',
            'is_approved' => true,
            'airing_date' => now()->subDay()->toDateString(),
        ]);

        // 4. No airing date -> active (backward compatibility)
        $noDateAnn = Announcement::create([
            'visitor_name' => 'No Date Sub',
            'visitor_phone' => '123456',
            'type' => 'general',
            'media' => 'tv',
            'content' => 'This has no date.',
            'word_count' => 4,
            'days_count' => 2,
            'rate_per_word' => 5,
            'total_amount' => 40,
            'payment_status' => 'paid',
            'is_approved' => true,
            'airing_date' => null,
        ]);

        $activeAnnouncements = Announcement::active()->get();

        $this->assertTrue($activeAnnouncements->contains($activeAnn));
        $this->assertFalse($activeAnnouncements->contains($futureAnn));
        $this->assertFalse($activeAnnouncements->contains($expiredAnn));
        $this->assertTrue($activeAnnouncements->contains($noDateAnn));
    }

    public function test_manager_role_can_manage_announcements_but_nothing_else(): void
    {
        $manager = User::factory()->create([
            'role' => 'manager'
        ]);

        // Manager can access admin announcements page
        $response = $this->actingAs($manager)->get('/admin/announcements');
        $response->assertOk();

        // Manager cannot access settings page
        $response = $this->actingAs($manager)->get('/admin/settings');
        $response->assertForbidden();

        // Manager cannot access users page
        $response = $this->actingAs($manager)->get('/admin/users');
        $response->assertForbidden();
    }

    public function test_admin_menus_manager_can_select_category(): void
    {
        $category = \App\Models\Category::create([
            'name' => 'Education News',
            'slug' => 'education',
            'order' => 10,
        ]);

        $component = Livewire::test('admin-menus-manager')
            ->set('selectedCategorySlug', 'education');
            
        $menuItems = $component->get('menuItems');
        // The last item in menuItems should be the education category
        $lastItem = end($menuItems);
        $this->assertEquals('Education News', $lastItem['label']);
        $this->assertEquals('/education', $lastItem['url']);
    }

    public function test_admin_menus_manager_can_save_menu_configuration(): void
    {
        Livewire::test('admin-menus-manager')
            ->set('menuItems', [
                ['label' => 'Custom Link', 'url' => '/custom', 'is_child' => false]
            ])
            ->call('saveMenu');

        $this->assertDatabaseHas('settings', [
            'key' => 'header_menu',
            'value' => json_encode([['label' => 'Custom Link', 'url' => '/custom', 'is_child' => false]])
        ]);
        
        $retrieved = \App\Models\Setting::get('header_menu');
        $this->assertEquals([['label' => 'Custom Link', 'url' => '/custom', 'is_child' => false]], $retrieved);
    }

    public function test_admin_menus_manager_can_indent_and_nest_items(): void
    {
        Livewire::test('admin-menus-manager')
            ->set('menuItems', [
                ['label' => 'News', 'url' => '/', 'is_child' => false],
                ['label' => 'Politics', 'url' => '/politics', 'is_child' => true],
                ['label' => 'Sports', 'url' => '/sports', 'is_child' => false]
            ])
            ->call('saveMenu');

        $expectedTree = [
            [
                'label' => 'News',
                'url' => '/',
                'is_child' => false,
                'children' => [
                    ['label' => 'Politics', 'url' => '/politics', 'is_child' => true]
                ]
            ],
            [
                'label' => 'Sports',
                'url' => '/sports',
                'is_child' => false
            ]
        ];

        $retrieved = \App\Models\Setting::get('header_menu');
        $this->assertEquals($expectedTree, $retrieved);
    }

    public function test_admin_can_save_advertising_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        Livewire::test('admin-settings-manager')
            ->set('activeTab', 'advertising')
            ->set('adsense_enabled', true)
            ->set('adsense_client_id', 'ca-pub-12345')
            ->set('adsense_code', '<script>adsense</script>')
            ->set('facebook_ads_enabled', true)
            ->set('facebook_ads_code', '<script>facebook</script>')
            ->set('custom_ads_enabled', false)
            ->set('ad_top_link', 'https://example.com')
            ->set('ad_footer_link', 'https://example.com/footer')
            ->set('ad_mobile_sticky_link', 'https://example.com/mobile')
            ->call('save');

        $this->assertTrue(Setting::get('adsense_enabled'));
        $this->assertEquals('ca-pub-12345', Setting::get('adsense_client_id'));
        $this->assertEquals('<script>adsense</script>', Setting::get('adsense_code'));
        $this->assertTrue(Setting::get('facebook_ads_enabled'));
        $this->assertEquals('<script>facebook</script>', Setting::get('facebook_ads_code'));
        $this->assertFalse(Setting::get('custom_ads_enabled'));
        $this->assertEquals('https://example.com', Setting::get('ad_top_link'));
        $this->assertEquals('https://example.com/footer', Setting::get('ad_footer_link'));
        $this->assertEquals('https://example.com/mobile', Setting::get('ad_mobile_sticky_link'));
    }

    public function test_admin_can_save_mpesa_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        Livewire::test('admin-settings-manager')
            ->set('activeTab', 'payments')
            ->set('mpesa_env', 'production')
            ->set('mpesa_shortcode', '654321')
            ->set('mpesa_consumer_key', 'testConsumerKey')
            ->set('mpesa_consumer_secret', 'testConsumerSecret')
            ->set('mpesa_passkey', 'testPasskey123')
            ->set('mpesa_initiator_name', 'testInitiator')
            ->set('mpesa_initiator_password', 'testInitiatorPassword')
            ->call('save');

        $this->assertEquals('production', Setting::get('mpesa_env'));
        $this->assertEquals('654321', Setting::get('mpesa_shortcode'));
        $this->assertEquals('testConsumerKey', Setting::get('mpesa_consumer_key'));
        $this->assertEquals('testConsumerSecret', Setting::get('mpesa_consumer_secret'));
        $this->assertEquals('testPasskey123', Setting::get('mpesa_passkey'));
        $this->assertEquals('testInitiator', Setting::get('mpesa_initiator_name'));
        $this->assertEquals('testInitiatorPassword', Setting::get('mpesa_initiator_password'));
        $this->assertEquals('M-Pesa', Setting::get('payment_methods'));
        $this->assertEquals('M-Pesa', Setting::get('payment_gateways'));
    }

    public function test_mpesa_stk_push_integration_logic(): void
    {
        Setting::set('mpesa_env', 'sandbox');
        Setting::set('mpesa_shortcode', '174379');
        Setting::set('mpesa_passkey', 'testPasskey');
        Setting::set('mpesa_consumer_key', 'testKey');
        Setting::set('mpesa_consumer_secret', 'testSecret');

        \Illuminate\Support\Facades\Http::fake([
            'oauth/v1/generate*' => \Illuminate\Support\Facades\Http::response([
                'access_token' => 'mock_daraja_access_token'
            ], 200),
            'mpesa/stkpush/v1/processrequest' => \Illuminate\Support\Facades\Http::response([
                'ResponseCode' => '0',
                'ResponseDescription' => 'Success. Request accepted for processing',
                'CheckoutRequestID' => 'ws_CO_15072026_xyz',
                'MerchantRequestID' => '12345'
            ], 200)
        ]);

        $result = \App\Support\Mpesa::stkPush('254712345678', 500, 'ANN-101');

        $this->assertTrue($result['success']);
        $this->assertEquals('ws_CO_15072026_xyz', $result['checkout_request_id']);
    }

    public function test_mpesa_webhook_callback_controller(): void
    {
        $announcement = Announcement::create([
            'visitor_name' => 'John Doe',
            'visitor_phone' => '254712345678',
            'type' => 'general',
            'media' => 'tv',
            'content' => 'This is a test announcement content.',
            'word_count' => 6,
            'days_count' => 5,
            'rate_per_word' => 5,
            'total_amount' => 150,
            'payment_status' => 'pending'
        ]);

        \Illuminate\Support\Facades\Cache::put('mpesa_ann_ws_CO_15072026_xyz', $announcement->id);

        $payload = [
            'Body' => [
                'stkCallback' => [
                    'MerchantRequestID' => '12345',
                    'CheckoutRequestID' => 'ws_CO_15072026_xyz',
                    'ResultCode' => 0,
                    'ResultDesc' => 'The service request is processed successfully.',
                    'CallbackMetadata' => [
                        'Item' => [
                            ['Name' => 'Amount', 'Value' => 1500],
                            ['Name' => 'MpesaReceiptNumber', 'Value' => 'QTR89SDFG3'],
                            ['Name' => 'TransactionDate', 'Value' => 20260715112259],
                            ['Name' => 'PhoneNumber', 'Value' => 254712345678]
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->postJson('/api/v1/payments/mpesa/callback', $payload);

        $response->assertStatus(200);
        $response->assertJson(['ResultCode' => 0]);

        $announcement->refresh();
        $this->assertEquals('paid', $announcement->payment_status);
        $this->assertEquals('QTR89SDFG3', $announcement->payment_reference);
    }

    public function test_mpesa_callback_url_resolution(): void
    {
        Setting::set('mpesa_callback_url', 'https://custom-domain.com/webhook');
        $this->assertEquals('https://custom-domain.com/webhook', \App\Support\Mpesa::getCallbackUrl());

        Setting::set('mpesa_callback_url', '');
        $this->assertNotEmpty(\App\Support\Mpesa::getCallbackUrl());
    }

    public function test_mpesa_manual_receipt_confirmation(): void
    {
        $announcement = Announcement::create([
            'visitor_name' => 'Emma Moraa',
            'visitor_phone' => '254712345678',
            'type' => 'funeral',
            'media' => 'both',
            'content' => 'Some funeral content text here.',
            'word_count' => 5,
            'rate_per_word' => 5,
            'days_count' => 3,
            'airing_date' => now()->toDateString(),
            'total_amount' => 1500,
            'payment_status' => 'pending',
            'submitter_type' => 'self',
        ]);

        \Livewire\Livewire::test(\App\Livewire\AnnouncementSubmit::class)
            ->set('currentAnnouncementId', $announcement->id)
            ->set('manual_receipt_ref', 'UGFI9B799B')
            ->call('confirmManualPayment')
            ->assertSet('mpesa_status', 'success');

        $announcement->refresh();
        $this->assertEquals('paid', $announcement->payment_status);
        $this->assertEquals('UGFI9B799B', $announcement->payment_reference);
    }
}
