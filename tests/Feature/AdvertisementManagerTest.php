<?php

namespace Tests\Feature;

use App\Models\Advertisement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class AdvertisementManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_advertisements_page()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/advertisements');
        $response->assertStatus(200);
    }

    public function test_admin_can_create_and_edit_advertisement_campaigns()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        Volt::test('admin-advertisements-manager')
            ->call('create')
            ->set('title', 'Highlands Realty Banner')
            ->set('location', 'top')
            ->set('destination_url', 'https://example.com/highlands')
            ->set('image_url', 'https://example.com/banner.jpg')
            ->set('starts_at', '2026-08-01T00:00')
            ->set('expires_at', '2026-12-31T23:59')
            ->set('is_active', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('advertisements', [
            'title' => 'Highlands Realty Banner',
            'location' => 'top',
            'destination_url' => 'https://example.com/highlands',
            'is_active' => 1,
        ]);

        $ad = Advertisement::where('title', 'Highlands Realty Banner')->first();
        $this->assertNotNull($ad);
        $this->assertEquals('2026-08-01', $ad->starts_at->format('Y-m-d'));

        // Toggle Active
        Volt::test('admin-advertisements-manager')
            ->call('toggleActive', $ad->id);

        $this->assertDatabaseHas('advertisements', [
            'id' => $ad->id,
            'is_active' => 0,
        ]);
    }

    public function test_active_db_advertisement_is_rendered_in_ad_partial()
    {
        Advertisement::create([
            'title' => 'Homepage Top Leaderboard',
            'location' => 'top',
            'image_url' => '/storage/ads/top_leaderboard.jpg',
            'destination_url' => 'https://example.com/promo',
            'is_active' => true,
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('/storage/ads/top_leaderboard.jpg');
        $response->assertSee('https://example.com/promo');
    }
}
