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

        Livewire::test('admin-menus-manager')
            ->call('selectCategory', 'education')
            ->assertSet('newLabel', 'Education News')
            ->assertSet('newUrl', '/education');
    }
}
