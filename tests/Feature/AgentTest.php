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

    public function test_admin_can_view_agent_details(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $agent = Agent::create([
            'name' => 'Samuel Mogaka',
            'location' => 'Kisii Town',
            'pin' => '2468',
            'commission_percentage' => 20,
        ]);

        $component = Livewire::actingAs($admin)
            ->test(\App\Livewire\AdminAgents::class)
            ->call('viewDetails', $agent->id)
            ->assertSet('isDetailsOpen', true)
            ->assertSet('selectedAgentForDetails.id', $agent->id);

        $component->call('closeDetails')
            ->assertSet('isDetailsOpen', false)
            ->assertSet('selectedAgentForDetails', null);
    }

    public function test_agent_pin_must_be_numeric_and_exactly_4_digits(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Test non-numeric PIN fails validation
        Livewire::actingAs($admin)
            ->test(\App\Livewire\AdminAgents::class)
            ->call('openForm')
            ->set('name', 'Samuel Mogaka')
            ->set('location', 'Kisii Town')
            ->set('pin', 'abcd') // non-numeric
            ->set('commission_percentage', 15)
            ->call('saveAgent')
            ->assertHasErrors(['pin']);

        // Test length !== 4 fails validation
        Livewire::actingAs($admin)
            ->test(\App\Livewire\AdminAgents::class)
            ->call('openForm')
            ->set('name', 'Samuel Mogaka')
            ->set('location', 'Kisii Town')
            ->set('pin', '123') // 3 digits
            ->set('commission_percentage', 15)
            ->call('saveAgent')
            ->assertHasErrors(['pin']);
    }

    public function test_agent_portal_login_flow(): void
    {
        $agent = Agent::create([
            'name' => 'Samuel Mogaka',
            'location' => 'Kisii Town',
            'pin' => '1357',
            'commission_percentage' => 15,
        ]);

        // 1. Visit announcements page - login modal is closed by default
        $submitComponent = Livewire::test(\App\Livewire\AnnouncementSubmit::class)
            ->assertSet('showAgentLoginModal', false);

        // 2. Open login modal
        $submitComponent->call('openAgentLogin')
            ->assertSet('showAgentLoginModal', true);

        // 3. Login with invalid PIN
        $submitComponent->set('login_pin', '9999')
            ->call('loginAsAgent')
            ->assertHasErrors(['login_pin'])
            ->assertSessionMissing('agent_logged_in');

        // 4. Login with valid PIN redirects to agent dashboard
        $submitComponent->set('login_pin', '1357')
            ->call('loginAsAgent')
            ->assertHasNoErrors()
            ->assertSessionHas('agent_logged_in', $agent->id);
    }

    public function test_guest_agent_is_redirected_from_dashboard(): void
    {
        // Access dashboard without session redirects back
        $this->get('/agent/dashboard')
            ->assertRedirect('/announcements');
    }

    public function test_authenticated_agent_can_access_dashboard(): void
    {
        $agent = Agent::create([
            'name' => 'Samuel Mogaka',
            'location' => 'Kisii Town',
            'pin' => '1357',
            'commission_percentage' => 15,
        ]);

        // Access dashboard with session loads profile
        $this->withSession(['agent_logged_in' => $agent->id])
            ->get('/agent/dashboard')
            ->assertOk();
    }

    public function test_agent_can_logout(): void
    {
        $agent = Agent::create([
            'name' => 'Samuel Mogaka',
            'location' => 'Kisii Town',
            'pin' => '1357',
            'commission_percentage' => 15,
        ]);

        // Logout agent session forgets session and redirects
        session(['agent_logged_in' => $agent->id]);
        Livewire::test(\App\Livewire\AgentDashboard::class)
            ->call('logoutAgent')
            ->assertRedirect('/announcements')
            ->assertSessionMissing('agent_logged_in');
    }

    public function test_admin_can_record_and_delete_agent_payouts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $agent = Agent::create([
            'name' => 'Samuel Mogaka',
            'location' => 'Kisii Town',
            'commission_percentage' => 20,
        ]);

        // Mock a paid announcement to give the agent commission
        Announcement::create([
            'visitor_name' => 'Emma Nyabera',
            'visitor_phone' => '+254712345678',
            'type' => 'funeral',
            'media' => 'tv',
            'content' => 'This is a test message.',
            'word_count' => 10,
            'days_count' => 1,
            'rate_per_word' => 5,
            'total_amount' => 50,
            'commission_amount' => 10, // 20% of 50
            'payment_status' => 'paid',
            'is_approved' => true,
            'agent_id' => $agent->id,
        ]);

        $this->assertEquals(10, $agent->fresh()->total_commission);
        $this->assertEquals(10, $agent->fresh()->commission_balance);

        // 1. Record Payout
        $component = Livewire::actingAs($admin)
            ->test(\App\Livewire\AdminAgents::class)
            ->call('viewDetails', $agent->id)
            ->set('payout_amount', 6)
            ->set('payout_method', 'M-Pesa')
            ->set('payout_reference', 'TESTTX12345')
            ->call('recordPayout');

        $component->assertHasNoErrors();
        $this->assertDatabaseHas('payouts', [
            'agent_id' => $agent->id,
            'amount' => 6,
            'payment_method' => 'M-Pesa',
            'reference' => 'TESTTX12345',
            'status' => 'completed',
        ]);

        $this->assertEquals(6, $agent->fresh()->total_payouts);
        $this->assertEquals(4, $agent->fresh()->commission_balance);

        $payout = \App\Models\Payout::where('reference', 'TESTTX12345')->first();

        // 2. Delete Payout
        $component->call('deletePayout', $payout->id);
        $component->assertHasNoErrors();
        
        $this->assertDatabaseMissing('payouts', [
            'id' => $payout->id
        ]);

        $this->assertEquals(0, $agent->fresh()->total_payouts);
        $this->assertEquals(10, $agent->fresh()->commission_balance);
    }

    public function test_agent_can_submit_disputes_and_admin_can_resolve(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $agent = Agent::create([
            'name' => 'Samuel Mogaka',
            'location' => 'Kisii Town',
            'pin' => '2468',
            'commission_percentage' => 20,
        ]);

        // 1. Submit Dispute from Agent Dashboard
        session(['agent_logged_in' => $agent->id]);
        $component = Livewire::test(\App\Livewire\AgentDashboard::class)
            ->set('dispute_subject', 'Commission missing for order #123')
            ->set('dispute_description', 'I submitted announcement #123 but did not receive commission.')
            ->call('fileDispute');

        $component->assertHasNoErrors();
        $this->assertDatabaseHas('disputes', [
            'agent_id' => $agent->id,
            'subject' => 'Commission missing for order #123',
            'description' => 'I submitted announcement #123 but did not receive commission.',
            'status' => 'open',
        ]);

        $dispute = \App\Models\Dispute::where('subject', 'Commission missing for order #123')->first();

        // 2. Admin Resolves the Dispute
        $adminComponent = Livewire::actingAs($admin)
            ->test(\App\Livewire\AdminAgents::class)
            ->call('viewDetails', $agent->id)
            ->set('dispute_resolution', 'Verified and added KSh 50 commission manually.')
            ->call('resolveDispute', $dispute->id, 'resolved');

        $adminComponent->assertHasNoErrors();
        $this->assertDatabaseHas('disputes', [
            'id' => $dispute->id,
            'status' => 'resolved',
            'resolution' => 'Verified and added KSh 50 commission manually.',
        ]);
    }

    public function test_sms_notification_triggers_on_announcement_submission_and_payment(): void
    {
        Setting::set('sms_notifications_enabled', true);
        Setting::set('sms_admin_phone', '+254711111111');
        Setting::set('sms_provider', 'mock');

        // Verify draft submission triggers SMS mock
        $component = Livewire::test(\App\Livewire\AnnouncementSubmit::class)
            ->set('visitor_name', 'Emma Nyabera')
            ->set('visitor_phone', '+254712345678')
            ->set('type', 'funeral')
            ->set('media', 'tv')
            ->set('days_count', 2)
            ->set('content', 'Draft content representing funeral announcement.')
            ->call('submitAnnouncement');

        $this->assertTrue(
            \App\Models\ContactMessage::where('name', 'SMS Gateway (Simulated)')
                ->where('subject', 'Admin SMS Notification (Recipient: +254711111111)')
                ->where('message', 'like', '%New announcement drafted%')
                ->exists()
        );

        // Verify payment confirmation triggers SMS mock
        $component->call('triggerMpesaStkPush');
        $component->call('confirmPaymentSuccess');

        $this->assertTrue(
            \App\Models\ContactMessage::where('name', 'SMS Gateway (Simulated)')
                ->where('subject', 'Admin SMS Notification (Recipient: +254711111111)')
                ->where('message', 'like', '%Payment received%')
                ->exists()
        );
    }

    public function test_textsms_gateway_trigger(): void
    {
        Setting::set('sms_notifications_enabled', true);
        Setting::set('sms_admin_phone', '+254711111111');
        Setting::set('sms_provider', 'textsms');
        
        // When config is missing, it should fall back to mock
        \App\Support\Sms::sendAdminNotification("Hello via TextSMS");

        $this->assertTrue(
            \App\Models\ContactMessage::where('name', 'SMS Gateway (Simulated)')
                ->where('subject', 'Admin SMS Notification (Recipient: +254711111111)')
                ->where('message', 'like', '%Hello via TextSMS [TextSMS Config Error]%')
                ->exists()
        );
    }

    public function test_admin_can_save_sms_templates_and_run_test_sms(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // 1. Save SMS Templates and Credentials via Livewire settings component
        $component = Livewire::actingAs($admin)
            ->test('admin-settings-manager', ['activeTab' => 'sms'])
            ->set('sms_notifications_enabled', true)
            ->set('sms_provider', 'mock')
            ->set('sms_admin_phone', '+254711223344')
            ->set('sms_template_draft', 'Custom Draft alert for [VisitorName]')
            ->set('sms_template_payment', 'Custom Payment alert for [VisitorName] ID: [AnnouncementId]')
            ->call('save');

        $component->assertHasNoErrors();
        $this->assertEquals('Custom Draft alert for [VisitorName]', Setting::get('sms_template_draft'));
        $this->assertEquals('Custom Payment alert for [VisitorName] ID: [AnnouncementId]', Setting::get('sms_template_payment'));

        // 2. Trigger Send Test SMS
        $component->set('test_sms_phone', '+254799887766')
            ->set('test_sms_message', 'Hello Test Message')
            ->call('sendTestSms');

        $component->assertHasNoErrors();
        $component->assertSet('test_sms_success', 'Test SMS successfully sent to +254799887766!');

        $this->assertTrue(
            \App\Models\ContactMessage::where('name', 'SMS Gateway (Simulated)')
                ->where('subject', 'Admin SMS Notification (Recipient: +254799887766)')
                ->where('message', 'like', '%Hello Test Message%')
                ->exists()
        );
    }
}
