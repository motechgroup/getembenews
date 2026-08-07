<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Agent;
use App\Models\Announcement;
use Livewire\Livewire;

class AnnouncementReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_announcement_reports_component_renders_for_authorized_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/announcements/reports');
        $response->assertStatus(200);
        $response->assertSee('Announcement Reports');
    }

    public function test_announcement_reports_calculates_kpi_stats_and_leaderboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $agent1 = Agent::create([
            'name' => 'Agent One',
            'business_name' => 'Agent One Shop',
            'phone' => '+254711111111',
            'location' => 'Kisii',
            'commission_percentage' => 10,
            'pin' => '1111',
        ]);

        $agent2 = Agent::create([
            'name' => 'Agent Two',
            'business_name' => 'Agent Two Shop',
            'phone' => '+254722222222',
            'location' => 'Nyamira',
            'commission_percentage' => 20,
            'pin' => '2222',
        ]);

        // Announcement 1 for Agent 1 (KSh 1,000, Comm KSh 100)
        Announcement::create([
            'visitor_name' => 'Client A',
            'visitor_phone' => '+254700000001',
            'type' => 'funeral',
            'media' => 'tv',
            'content' => 'Funeral announcement test one content sample',
            'airing_date' => now()->toDateString(),
            'days_count' => 1,
            'word_count' => 10,
            'rate_per_word' => 5,
            'total_amount' => 1000,
            'payment_status' => 'paid',
            'payment_reference' => 'MPESA1000',
            'is_approved' => true,
            'agent_id' => $agent1->id,
            'commission_amount' => 100,
        ]);

        // Announcement 2 for Agent 2 (KSh 3,000, Comm KSh 600)
        Announcement::create([
            'visitor_name' => 'Client B',
            'visitor_phone' => '+254700000002',
            'type' => 'general',
            'media' => 'radio',
            'content' => 'General notice test two content sample',
            'airing_date' => now()->toDateString(),
            'days_count' => 2,
            'word_count' => 20,
            'rate_per_word' => 5,
            'total_amount' => 3000,
            'payment_status' => 'paid',
            'payment_reference' => 'MPESA3000',
            'is_approved' => true,
            'agent_id' => $agent2->id,
            'commission_amount' => 600,
        ]);

        Livewire::actingAs($admin)
            ->test(\App\Livewire\AdminAnnouncementReports::class)
            ->assertSee('Agent Two')
            ->assertSee('Agent One')
            ->assertSee('KSh 4,000') // Gross revenue
            ->assertSee('KSh 700'); // Total commissions
    }

    public function test_announcement_reports_csv_export(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Announcement::create([
            'visitor_name' => 'Export Client',
            'visitor_phone' => '+254799887766',
            'type' => 'funeral',
            'media' => 'both',
            'content' => 'Export report content line test',
            'airing_date' => now()->toDateString(),
            'days_count' => 1,
            'word_count' => 5,
            'rate_per_word' => 7,
            'total_amount' => 500,
            'payment_status' => 'paid',
            'payment_reference' => 'MPESA500',
            'is_approved' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(\App\Livewire\AdminAnnouncementReports::class)
            ->call('exportCsv')
            ->assertFileDownloaded();
    }
}
