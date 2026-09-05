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
            $mobileRedirect = request('redirect_uri');
            
            $state = Str::random(32);
            if (!empty($mobileRedirect)) {
                $state = 'mobile_' . base64_encode($mobileRedirect) . '_' . $state;
                session(['mobile_redirect_uri' => $mobileRedirect]);
            }
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

        $state = $request->input('state', '');
        $mobileRedirectUri = session('mobile_redirect_uri');
        if (empty($mobileRedirectUri) && str_starts_with($state, 'mobile_')) {
            $parts = explode('_', $state);
            if (isset($parts[1]) && !empty($parts[1])) {
                $mobileRedirectUri = base64_decode($parts[1]);
            }
        }

        if ($request->has('error') || $request->has('denied')) {
            if ($mobileRedirectUri) {
                $separator = str_contains($mobileRedirectUri, '?') ? '&' : '?';
                return redirect($mobileRedirectUri . $separator . 'error=cancelled');
            }
            return redirect()->route('login')->with('error', ucfirst($provider) . ' sign-in was cancelled.');
        }

        if (!$request->filled('code')) {
            if ($mobileRedirectUri) {
                $separator = str_contains($mobileRedirectUri, '?') ? '&' : '?';
                return redirect($mobileRedirectUri . $separator . 'error=missing_code');
            }
            return redirect()->route('login')->with('error', 'Missing authorization code from ' . ucfirst($provider) . '.');
        }

        if ($provider === 'google') {
            $clientId = trim(Setting::get('google_client_id', ''));
            $clientSecret = trim(Setting::get('google_client_secret', ''));

            if (empty($clientId) || empty($clientSecret)) {
                if ($mobileRedirectUri) {
                    $separator = str_contains($mobileRedirectUri, '?') ? '&' : '?';
                    return redirect($mobileRedirectUri . $separator . 'error=unconfigured');
                }
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
                if ($mobileRedirectUri) {
                    $separator = str_contains($mobileRedirectUri, '?') ? '&' : '?';
                    return redirect($mobileRedirectUri . $separator . 'error=' . urlencode($err));
                }
                return redirect()->route('login')->with('error', $err);
            }

            $accessToken = $tokenResponse->json('access_token');
            $userResponse = Http::withToken($accessToken)->get('https://www.googleapis.com/oauth2/v3/userinfo');

            if ($userResponse->failed()) {
                if ($mobileRedirectUri) {
                    $separator = str_contains($mobileRedirectUri, '?') ? '&' : '?';
                    return redirect($mobileRedirectUri . $separator . 'error=profile_failed');
                }
                return redirect()->route('login')->with('error', 'Failed to retrieve user profile from Google.');
            }

            $userData = $userResponse->json();
            $email = strtolower(trim($userData['email'] ?? ''));

            if (empty($email)) {
                if ($mobileRedirectUri) {
                    $separator = str_contains($mobileRedirectUri, '?') ? '&' : '?';
                    return redirect($mobileRedirectUri . $separator . 'error=no_email');
                }
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

            if (!empty($mobileRedirectUri)) {
                session()->forget('mobile_redirect_uri');
                $sanctumToken = $user->createToken('mobile-google-app')->plainTextToken;
                $separator = str_contains($mobileRedirectUri, '?') ? '&' : '?';
                $appRedirect = $mobileRedirectUri . $separator . 'token=' . urlencode($sanctumToken);

                return response()->make("
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset='utf-8'>
                        <meta name='viewport' content='width=device-width, initial-scale=1'>
                        <title>Authenticating with Getembe News...</title>
                        <script>
                            window.location.href = '" . addslashes($appRedirect) . "';
                        </script>
                    </head>
                    <body style='font-family: system-ui, -apple-system, sans-serif; text-align: center; padding: 50px 20px; background: #0f172a; color: #fff;'>
                        <h2 style='color: #cc6c3b;'>Authentication Successful</h2>
                        <p>Returning to Getembe News App...</p>
                        <a href='" . htmlspecialchars($appRedirect, ENT_QUOTES) . "' style='display: inline-block; margin-top: 15px; padding: 12px 24px; background: #cc6c3b; color: #fff; text-decoration: none; font-weight: bold; border-radius: 8px;'>Tap here to return to App</a>
                    </body>
                    </html>
                ", 200, ['Content-Type' => 'text/html']);
            }

            if ($user->isStaff()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('dashboard'));
        }

        return redirect()->route('login')->with('error', 'Provider ' . $provider . ' login handling is not configured.');
    }
}
