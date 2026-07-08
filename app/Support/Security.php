<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rules\Password;

class Security
{
    /**
     * Check if an email is blacklisted or matches a wildcard blacklist pattern.
     */
    public static function isBlacklisted(string $email): bool
    {
        $blacklist = Setting::get('email_blacklist', '');
        if (empty($blacklist)) {
            return false;
        }

        $emails = array_map('trim', explode(',', str_replace(["\r\n", "\r", "\n"], ',', $blacklist)));
        $email = strtolower($email);

        foreach ($emails as $pattern) {
            if (empty($pattern)) {
                continue;
            }
            $pattern = strtolower($pattern);
            // Replace wildcard * with regex equivalent
            $regex = '/^' . str_replace('\*', '.*', preg_quote($pattern, '/')) . '$/';
            if (preg_match($regex, $email)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verify reCAPTCHA or Cloudflare Turnstile token.
     */
    public static function verifyCaptcha(?string $token): bool
    {
        $driver = Setting::get('captcha_driver', 'none');
        if ($driver === 'none') {
            return true;
        }
        if (empty($token)) {
            return false;
        }

        $url = '';
        $secret = '';

        if ($driver === 'recaptcha') {
            $url = 'https://www.google.com/recaptcha/api/siteverify';
            $secret = Setting::get('recaptcha_secret_key');
        } elseif ($driver === 'turnstile') {
            $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
            $secret = Setting::get('turnstile_secret_key');
        }

        if (empty($secret)) {
            return true; // Pass if key is not configured to avoid blocking users
        }

        try {
            $response = Http::asForm()->post($url, [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => request()->ip()
            ]);
            return $response->json('success') === true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Build password complexity rules dynamically.
     */
    public static function passwordRules(): Password
    {
        $minLength = (int) Setting::get('password_min_length', 8);
        $complexity = Setting::get('password_complexity_required', false);

        $rules = Password::min($minLength);
        if ($complexity) {
            $rules->letters()->mixedCase()->numbers()->symbols();
        }

        return $rules;
    }

    /**
     * Sanitize user content (comments) to strip external links or apply nofollow.
     */
    public static function sanitizeUserContent(string $text): string
    {
        $nofollow = Setting::get('seo_nofollow_links', true);
        $strip = Setting::get('seo_strip_links', false);

        // Core protection: First strip HTML tags to prevent XSS (since comments are rich text input, but we want plain text)
        $text = strip_tags($text);

        if ($strip) {
            // Replace URL-like words with [link removed]
            $text = preg_replace('/\bhttps?:\/\/[^\s<>"\']+/i', '[link removed]', $text);
            $text = preg_replace('/\bwww\.[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+[^\s<>"\']*/i', '[link removed]', $text);
        } elseif ($nofollow) {
            // Wrap any plaintext URLs in a <a> tag with nofollow, except local ones
            $siteHost = parse_url(url('/'), PHP_URL_HOST);
            $text = preg_replace_callback('/\b(https?:\/\/[^\s<>"\']+)/i', function ($matches) use ($siteHost) {
                $url = $matches[1];
                $host = parse_url($url, PHP_URL_HOST);
                if ($host && $host !== $siteHost) {
                    return '<a href="' . $url . '" rel="nofollow" target="_blank" class="text-blue-600 hover:underline">' . $url . '</a>';
                }
                return '<a href="' . $url . '" target="_blank" class="text-blue-600 hover:underline">' . $url . '</a>';
            }, $text);
        }

        return $text;
    }

    /**
     * Check if a login email address is currently locked out.
     */
    public static function isAccountLocked(string $email): bool
    {
        return Cache::has('account_lockout:' . strtolower($email));
    }

    /**
     * Retrieve remaining lockout seconds.
     */
    public static function lockoutRemaining(string $email): int
    {
        $lockoutTime = Cache::get('account_lockout:' . strtolower($email));
        if (!$lockoutTime) {
            return 0;
        }
        return max(0, $lockoutTime - time());
    }

    /**
     * Lock an account due to brute-force attempts.
     */
    public static function lockAccount(string $email, int $seconds = 900): void
    {
        Cache::put('account_lockout:' . strtolower($email), time() + $seconds, $seconds);
    }

    /**
     * Track and increment failed login count.
     */
    public static function incrementFailedLogin(string $email, int $maxAttempts = 5, int $lockoutSeconds = 900): bool
    {
        $key = 'failed_login_count:' . strtolower($email);
        $attempts = (int) Cache::get($key, 0) + 1;
        Cache::put($key, $attempts, 900); // Track attempts for 15 mins

        if ($attempts >= $maxAttempts) {
            self::lockAccount($email, $lockoutSeconds);
            Cache::forget($key); // Reset counter once locked
            return true;
        }

        return false;
    }

    /**
     * Clear failed login history for an email.
     */
    public static function clearFailedLogin(string $email): void
    {
        Cache::forget('failed_login_count:' . strtolower($email));
        Cache::forget('account_lockout:' . strtolower($email));
    }
}
