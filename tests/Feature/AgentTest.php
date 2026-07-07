<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Agent;
use App\Models\Announcement;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AgentTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_or_admin_can_access_agents_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $manager = User::factory()->create(['role' => 'manager']);
        $editor = User::factory()->create(['role' => 'editor']); // Editor also has access due to default permission map
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($admin)->get('/admin/agents')->assertOk();
        $this->actingAs($manager)->get('/admin/agents')->assertOk();
        $this->actingAs($editor)->get('/admin/agents')->assertOk();
        $this->actingAs($user)->get('/admin/agents')->assertForbidden();
    }

    public function test_agent_crud_operations_by_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // 1. Create Agent
        $component = Livewire::actingAs($admin)
            ->test(\App\Livewire\AdminAgents::class)
            ->call('openForm')
            ->set('name', 'Samuel Mogaka')
            ->set('location', 'Kisii Town')
            ->set('pin', '9999')
            ->set('commission_percentage', 15)
            ->call('saveAgent');

        $component->assertHasNoErrors();
        $this->assertDatabaseHas('agents', [
            'name' => 'Samuel Mogaka',
            'location' => 'Kisii Town',
            'pin' => '9999',
            'commission_percentage' => 15,
        ]);

        $agent = Agent::where('name', 'Samuel Mogaka')->first();

        // 2. Update Agent
        Livewire::actingAs($admin)
            ->test(\App\Livewire\AdminAgents::class)
            ->call('openForm', $agent->id)
            ->set('commission_percentage', 20)
            ->call('saveAgent')
            ->assertHasNoErrors();

        $this->assertEquals(20, $agent->fresh()->commission_percentage);

        // 3. Delete Agent
        Livewire::actingAs($admin)
            ->test(\App\Livewire\AdminAgents::class)
            ->call('deleteAgent', $agent->id);

        $this->assertDatabaseMissing('agents', [
            'id' => $agent->id
        ]);
    }

    public function test_announcement_submission_on_behalf_of_agent_calculates_commission(): void
    {
        Setting::set('announcement_rate_tv', '5');

        $agent = Agent::create([
            'name' => 'Samuel Mogaka',
            'location' => 'Kisii Town',
            'pin' => '2468',
            'commission_percentage' => 20, // 20% commission
        ]);

        $component = Livewire::test(\App\Livewire\AnnouncementSubmit::class)
            ->set('visitor_name', 'Emma Nyabera')
            ->set('visitor_phone', '+254712345678')
            ->set('type', 'funeral')
            ->set('media', 'tv')
            ->set('days_count', 3)
            ->set('content', 'This is a test funeral announcement containing exactly eight words here.')
            ->set('submitter_type', 'agent')
            ->set('agent_pin', '2468');

        $component->call('submitAnnouncement');

        $component->assertSet('showCheckoutModal', true);
        $this->assertDatabaseHas('announcements', [
            'visitor_name' => 'Emma Nyabera',
            'agent_id' => $agent->id,
            'payment_status' => 'pending',
            'commission_amount' => 0, // Commission is 0 until paid
        ]);

        $announcementId = $component->get('currentAnnouncementId');

        $component->call('triggerMpesaStkPush');
        $component->call('confirmPaymentSuccess');
        $component->assertSet('mpesa_status', 'success');

        // Total price is 11 words * 5 KSh/word * 3 days = 165 KSh
        // Commission is 20% of 165 = 33 KSh
        $this->assertDatabaseHas('announcements', [
            'id' => $announcementId,
            'payment_status' => 'paid',
            'commission_amount' => 33,
        ]);
    }

    public function test_admin_manually_marking_agent_announcement_paid_calculates_commission(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $agent = Agent::create([
            'name' => 'Samuel Mogaka',
            'location' => 'Kisii Town',
            'commission_percentage' => 15,
        ]);

        $announcement = Announcement::create([
            'visitor_name' => 'Emma Nyabera',
            'visitor_phone' => '+254712345678',
            'type' => 'funeral',
            'media' => 'tv',
            'content' => 'This is a test message.',
            'word_count' => 5,
            'days_count' => 2,
            'rate_per_word' => 5,
            'total_amount' => 50, // 5 * 2 * 5 = 50
            'payment_status' => 'pending',
            'is_approved' => false,
            'agent_id' => $agent->id,
        ]);

        Livewire::actingAs($admin)
            ->test(\App\Livewire\AdminAnnouncements::class)
            ->call('markAsPaid', $announcement->id)
            ->assertHasNoErrors();

        // Commission is 15% of 50 = 7.5 -> rounded to 8 KSh or 7 KSh depending on rounding.
        // 50 * 15 / 100 = 7.5. round(7.5) = 8.
        $this->assertEquals(8, $announcement->fresh()->commission_amount);
        $this->assertEquals('paid', $announcement->fresh()->payment_status);
    }

    public function test_announcement_submission_fails_with_invalid_agent_pin(): void
    {
        $component = Livewire::test(\App\Livewire\AnnouncementSubmit::class)
            ->set('visitor_name', 'Emma Nyabera')
            ->set('visitor_phone', '+254712345678')
            ->set('type', 'funeral')
            ->set('media', 'tv')
            ->set('days_count', 3)
            ->set('content', 'This is a test funeral announcement containing exactly eight words here.')
            ->set('submitter_type', 'agent')
            ->set('agent_pin', '9999'); // Invalid PIN

        $component->call('submitAnnouncement');

        $component->assertHasErrors(['agent_pin']);
        $component->assertSet('showCheckoutModal', false);
    }
}
