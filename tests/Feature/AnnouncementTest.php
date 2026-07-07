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

        // Verify system draft alert was logged to contact messages
        $this->assertDatabaseHas('contact_messages', [
            'name' => 'System Alert',
            'subject' => 'New Announcement Submitted (Pending Payment)',
        ]);

        $announcementId = $component->get('currentAnnouncementId');

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
}
