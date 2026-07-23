<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GoogleSocialAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_redirect_fails_when_disabled()
    {
        Setting::set('google_login', '0');

        $response = $this->get('/auth/google');

        $response->assertRedirect('/login')
            ->assertSessionHas('error', 'Google login is currently disabled.');
    }

    public function test_google_redirect_succeeds_when_enabled_and_configured()
    {
        Setting::set('google_login', '1');
        Setting::set('google_client_id', 'fake-google-client-id.apps.googleusercontent.com');

        $response = $this->get('/auth/google');

        $response->assertRedirect();
        $this->assertStringContainsString('accounts.google.com', $response->headers->get('Location'));
        $this->assertStringContainsString('fake-google-client-id', $response->headers->get('Location'));
    }

    public function test_google_callback_authenticates_user_successfully()
    {
        Setting::set('google_login', '1');
        Setting::set('google_client_id', 'fake-client-id');
        Setting::set('google_client_secret', 'fake-client-secret');

        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'mock-access-token-123',
            ], 200),
            'https://www.googleapis.com/oauth2/v3/userinfo' => Http::response([
                'email' => 'googleuser@example.com',
                'name' => 'Google User',
                'picture' => 'https://example.com/photo.jpg',
            ], 200),
        ]);

        $response = $this->get('/auth/google/callback?code=mock-code-xyz');

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'googleuser@example.com',
            'name' => 'Google User',
            'role' => 'subscriber',
        ]);
    }
}
