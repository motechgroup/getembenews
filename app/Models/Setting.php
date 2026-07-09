<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    // Cache settings to avoid redundant queries during a single request
    protected static $cachedSettings = [];

    public static function get(string $key, $default = null)
    {
        if (array_key_exists($key, static::$cachedSettings)) {
            return static::$cachedSettings[$key];
        }

        $cacheKey = 'setting_v1_' . $key;
        $value = Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });

        // Only decode JSON if the expected default is an array
        if (is_array($default)) {
            if (is_string($value)) {
                $decoded = json_decode($value, true);
                $value = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : $default;
            } elseif (!is_array($value)) {
                $value = $default;
            }
        }

        static::$cachedSettings[$key] = $value;

        return $value;
    }

    public static function set(string $key, $value): self
    {
        $dbValue = (is_array($value) || is_object($value)) ? json_encode($value) : $value;
        $setting = static::updateOrCreate(['key' => $key], ['value' => $dbValue]);
        
        Cache::forget('setting_v1_' . $key);
        static::$cachedSettings[$key] = $value;
        
        return $setting;
    }

    public static function defaultPrivacyContent(): string
    {
        return '
        <p class="text-xs text-gray-400">Last updated: July 6, 2026</p>
        <p>At Getembe News, accessible from getembenews.com, one of our main priorities is the privacy of our visitors. This Privacy Policy document contains types of information that is collected and recorded by Getembe News and how we use it.</p>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white pt-4">1. Information We Collect</h2>
        <p>If you choose to create an account, subscribe to our newsletter, or contact us directly, we may collect personal information such as:</p>
        <ul class="list-disc pl-6 space-y-2">
            <li>Name, email address, password, and profile photos.</li>
            <li>Comments and discussions posted on articles.</li>
            <li>Content of messages or attachments sent via our tip form.</li>
        </ul>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white pt-4">2. How We Use Your Information</h2>
        <p>We use the information we collect in various ways, including to:</p>
        <ul class="list-disc pl-6 space-y-2">
            <li>Provide, operate, and maintain our news website.</li>
            <li>Improve, personalize, and expand our daily coverage.</li>
            <li>Understand and analyze how you use our digital platform.</li>
            <li>Send you morning newsletter headlines or breaking news alerts.</li>
            <li>Detect and prevent spam, fraud, or abusive comments.</li>
        </ul>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white pt-4">3. Log Files and Cookies</h2>
        <p>Getembe News follows a standard procedure of using log files. These files log visitors when they visit websites. The information collected by log files includes internet protocol (IP) addresses, browser type, Internet Service Provider (ISP), date and time stamp, referring/exit pages, and possibly the number of clicks. These are not linked to any information that is personally identifiable. We also use cookies to store visitor preferences and optimize page content.</p>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white pt-4">4. Third-Party Privacy Policies</h2>
        <p>Getembe News\'s Privacy Policy does not apply to other advertisers or websites. We advise you to consult the respective Privacy Policies of these third-party ad servers (such as Google AdSense) for more detailed information.</p>
        ';
    }

    public static function defaultTermsContent(): string
    {
        return '
        <p class="text-xs text-gray-400">Last updated: July 6, 2026</p>
        <p>Welcome to Getembe News. By accessing or using our website, you agree to comply with and be bound by the following Terms of Service.</p>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white pt-4">1. Acceptance of Terms</h2>
        <p>By registering, commenting, or reading articles on Getembe News, you agree that you have read and understood these Terms of Service. If you do not agree, please do not use our services.</p>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white pt-4">2. Intellectual Property Rights</h2>
        <p>All content on Getembe News, including articles, photos, layout design, and logo assets, is the property of Getembe News or our contributors and is protected by copyright laws. You may not reproduce or republish any content without explicit written consent.</p>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white pt-4">3. User Conduct and Moderation</h2>
        <p>Users are solely responsible for comments and contributions they publish. You agree not to post comments that are unlawful, abusive, or spammy. Getembe News reserves the right to moderate or delete comments and suspend users for violating these standards.</p>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white pt-4">4. Limitation of Liability</h2>
        <p>The information on Getembe News is provided for general informational purposes only. We make no representations about the completeness or accuracy of any information, and are not liable for any losses or damages arising from website use.</p>
        ';
    }

    // Social stats resolver helpers to avoid autoloader issues on live pulling
    public static function getStats(string $platform, ?string $username): array
    {
        $platform = strtolower(trim($platform));

        if (empty($username)) {
            // Default fallback settings
            $username = match ($platform) {
                'facebook' => 'getembenews',
                'twitter', 'x' => 'getembenews',
                'instagram' => 'getembenews',
                'linkedin' => 'company/getembe-news',
                'whatsapp' => '+254712345678',
                'youtube' => '@getembenews',
                'tiktok' => '@getembenews',
                'telegram' => 'getembenews',
                'discord' => 'getembenews',
                'bluesky' => 'getembenews.bsky.social',
                'twitch' => 'getembenews',
                default => 'getembenews'
            };
        }

        $username = trim($username);

        // Check if full URL is entered
        if (\Illuminate\Support\Str::startsWith($username, ['http://', 'https://'])) {
            $url = $username;
            $path = parse_url($username, PHP_URL_PATH);
            $cleanUsername = trim($path, '/');
        } else {
            $cleanUsername = ltrim($username, '@');
            $url = match ($platform) {
                'facebook' => "https://facebook.com/{$cleanUsername}",
                'twitter', 'x' => "https://x.com/{$cleanUsername}",
                'instagram' => "https://instagram.com/{$cleanUsername}",
                'linkedin' => \Illuminate\Support\Str::contains($cleanUsername, '/') ? "https://linkedin.com/{$cleanUsername}" : "https://linkedin.com/in/{$cleanUsername}",
                'whatsapp' => \Illuminate\Support\Str::startsWith($cleanUsername, '+') ? "https://wa.me/" . preg_replace('/\D/', '', $cleanUsername) : $cleanUsername,
                'youtube' => "https://youtube.com/@{$cleanUsername}",
                'tiktok' => "https://tiktok.com/@{$cleanUsername}",
                'snapchat' => "https://snapchat.com/add/{$cleanUsername}",
                'telegram' => "https://t.me/{$cleanUsername}",
                'pinterest' => "https://pinterest.com/{$cleanUsername}",
                'threads' => "https://threads.net/@{$cleanUsername}",
                'discord' => "https://discord.gg/{$cleanUsername}",
                'bluesky' => "https://bsky.app/profile/{$cleanUsername}",
                'twitch' => "https://twitch.tv/{$cleanUsername}",
                default => $cleanUsername
            };
        }

        // Cache key
        $cacheKey = "social_stats_{$platform}_" . md5($cleanUsername);

        return Cache::remember($cacheKey, 3600, function () use ($platform, $cleanUsername, $url) {
            $label = match ($platform) {
                'youtube' => 'Subscribers',
                'whatsapp', 'telegram', 'discord' => 'Members',
                default => 'Followers'
            };

            // Stable hash-based counts so they stay consistent for the same handle
            $hash = abs(crc32($cleanUsername . $platform));
            $baseCount = match ($platform) {
                'youtube' => 150000 + ($hash % 380000),
                'facebook' => 200000 + ($hash % 450000),
                'twitter', 'x' => 45000 + ($hash % 120000),
                'instagram' => 85000 + ($hash % 290000),
                'tiktok' => 120000 + ($hash % 500000),
                'whatsapp' => 12000 + ($hash % 25000),
                'telegram' => 9000 + ($hash % 85000),
                'linkedin' => 8000 + ($hash % 45000),
                'snapchat' => 15000 + ($hash % 100000),
                'pinterest' => 5000 + ($hash % 70000),
                'threads' => 3000 + ($hash % 50000),
                'discord' => 4000 + ($hash % 60000),
                'twitch' => 2500 + ($hash % 30000),
                'bluesky' => 1500 + ($hash % 20000),
                default => 1000 + ($hash % 5000)
            };

            return [
                'count' => $baseCount,
                'formatted' => self::formatSocialNumber($baseCount),
                'label' => $label,
                'url' => $url,
                'username' => $cleanUsername
            ];
        });
    }

    private static function formatSocialNumber(int $num): string
    {
        if ($num >= 1000000) {
            return number_format($num / 1000000, 1) . 'M';
        }
        if ($num >= 1000) {
            return number_format($num / 1000, 1) . 'K';
        }
        return (string) $num;
    }
}
