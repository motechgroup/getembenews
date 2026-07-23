<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Redirect user to social provider OAuth page.
     */
    public function redirectToProvider(string $provider)
    {
        $provider = strtolower($provider);
        $enabled = (bool) Setting::get("{$provider}_login", false);

        if (!$enabled) {
            return redirect()->route('login')->with('error', ucfirst($provider) . ' login is currently disabled.');
        }

        if ($provider === 'google') {
            $clientId = trim(Setting::get('google_client_id', ''));
            if (empty($clientId)) {
                return redirect()->route('login')->with('error', 'Google OAuth client ID is not configured in Admin Settings.');
            }

            $redirectUri = url('/auth/google/callback');
            $state = Str::random(40);
            session(['oauth_state_' . $provider => $state]);

            $query = http_build_query([
                'client_id' => $clientId,
                'redirect_uri' => $redirectUri,
                'response_type' => 'code',
                'scope' => 'openid email profile',
                'state' => $state,
                'prompt' => 'select_account',
            ]);

            return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $query);
        }

        return redirect()->route('login')->with('error', 'Unsupported social login provider: ' . $provider);
    }

    /**
     * Handle provider OAuth callback.
     */
    public function handleProviderCallback(string $provider, Request $request)
    {
        $provider = strtolower($provider);

        if ($request->has('error') || $request->has('denied')) {
            return redirect()->route('login')->with('error', ucfirst($provider) . ' sign-in was cancelled.');
        }

        if (!$request->filled('code')) {
            return redirect()->route('login')->with('error', 'Missing authorization code from ' . ucfirst($provider) . '.');
        }

        if ($provider === 'google') {
            $clientId = trim(Setting::get('google_client_id', ''));
            $clientSecret = trim(Setting::get('google_client_secret', ''));

            if (empty($clientId) || empty($clientSecret)) {
                return redirect()->route('login')->with('error', 'Google OAuth credentials are missing in Admin Settings.');
            }

            $redirectUri = url('/auth/google/callback');

            $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => $request->input('code'),
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
            ]);

            if ($tokenResponse->failed()) {
                Log::error('Google OAuth Token Exchange Failed', ['body' => $tokenResponse->body()]);
                $err = $tokenResponse->json()['error_description'] ?? 'Failed to exchange authorization token with Google.';
                return redirect()->route('login')->with('error', $err);
            }

            $accessToken = $tokenResponse->json('access_token');
            $userResponse = Http::withToken($accessToken)->get('https://www.googleapis.com/oauth2/v3/userinfo');

            if ($userResponse->failed()) {
                return redirect()->route('login')->with('error', 'Failed to retrieve user profile from Google.');
            }

            $userData = $userResponse->json();
            $email = strtolower(trim($userData['email'] ?? ''));

            if (empty($email)) {
                return redirect()->route('login')->with('error', 'Google account did not return a valid email address.');
            }

            $name = trim($userData['name'] ?? explode('@', $email)[0]);
            $picture = $userData['picture'] ?? null;

            $user = User::where('email', $email)->first();

            if ($user) {
                if (!$user->email_verified_at) {
                    $user->update(['email_verified_at' => now()]);
                }
                if (empty($user->photo_url) && !empty($picture)) {
                    $user->update(['photo_url' => $picture]);
                }
            } else {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make(Str::random(32)),
                    'role' => 'subscriber',
                    'photo_url' => $picture,
                    'email_verified_at' => now(),
                ]);
            }

            Auth::login($user, true);
            $request->session()->regenerate();

            if ($user->isStaff()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('dashboard'));
        }

        return redirect()->route('login')->with('error', 'Provider ' . $provider . ' login handling is not configured.');
    }
}
