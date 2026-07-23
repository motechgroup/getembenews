<?php

namespace Tests\Feature;

use App\Models\Advertisement;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileApiNativeAdsTest extends TestCase
{
    use RefreshDatabase;

    public function test_mobile_app_settings_returns_native_ad_configurations()
    {
        Setting::set('mobile_app_native_ads_enabled', '1');
        Setting::set('mobile_app_admob_native_id', 'ca-app-pub-test/native123');
        Setting::set('mobile_app_facebook_native_id', 'FB_NATIVE_123');
        Setting::set('mobile_app_native_ad_code', '<div class="native-ad">Native Code</div>');
        Setting::set('mobile_app_native_ad_frequency', '4');

        $response = $this->getJson('/api/v1/app-settings');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'mobile_app_native_ads_enabled' => true,
                    'mobile_app_admob_native_id' => 'ca-app-pub-test/native123',
                    'mobile_app_facebook_native_id' => 'FB_NATIVE_123',
                    'mobile_app_native_ad_code' => '<div class="native-ad">Native Code</div>',
                    'mobile_app_native_ad_frequency' => 4,
                ]
            ]);
    }

    public function test_native_ads_endpoint_delivers_active_native_ads()
    {
        Setting::set('mobile_app_native_ads_enabled', '1');
        Setting::set('mobile_app_admob_native_id', 'ca-app-pub-test/native123');

        $nativeAd = Advertisement::create([
            'title' => 'Native Mobile Promo',
            'image_url' => 'https://example.com/native-ad.jpg',
            'destination_url' => 'https://example.com/promo',
            'location' => 'mobile_native',
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/native-ads');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'enabled' => true,
                    'admob_native_id' => 'ca-app-pub-test/native123',
                ]
            ])
            ->assertJsonFragment([
                'title' => 'Native Mobile Promo',
                'location' => 'mobile_native',
            ]);
    }

    public function test_advertisements_endpoint_supports_mobile_native_location_filter()
    {
        $nativeAd = Advertisement::create([
            'title' => 'Native In-Feed Campaign',
            'image_url' => 'https://example.com/feed-ad.jpg',
            'destination_url' => 'https://example.com/landing',
            'location' => 'mobile_native',
            'is_active' => true,
        ]);

        $sidebarAd = Advertisement::create([
            'title' => 'Sidebar Banner',
            'image_url' => 'https://example.com/sidebar.jpg',
            'location' => 'sidebar',
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/advertisements?location=mobile_native');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'title' => 'Native In-Feed Campaign',
                'location' => 'mobile_native',
            ]);
    }

    public function test_home_feed_includes_native_ad_payload()
    {
        Setting::set('mobile_app_native_ads_enabled', '1');
        Setting::set('mobile_app_native_ad_frequency', '3');

        $nativeAd = Advertisement::create([
            'title' => 'Sponsor In Feed',
            'image_url' => 'https://example.com/sponsor.jpg',
            'location' => 'mobile_native',
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/home-feed');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'enabled' => true,
                'frequency' => 3,
            ])
            ->assertJsonFragment([
                'title' => 'Sponsor In Feed',
            ]);
    }
}
