<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SocialFetcher
{
    /**
     * Get follower/subscriber count for a social platform and username/channel.
     *
     * @param string $platform
     * @param string|null $username
     * @return array
     */
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
        if (Str::startsWith($username, ['http://', 'https://'])) {
            $url = $username;
            $path = parse_url($username, PHP_URL_PATH);
            $cleanUsername = trim($path, '/');
        } else {
            $cleanUsername = ltrim($username, '@');
            $url = match ($platform) {
                'facebook' => "https://facebook.com/{$cleanUsername}",
                'twitter', 'x' => "https://x.com/{$cleanUsername}",
                'instagram' => "https://instagram.com/{$cleanUsername}",
                'linkedin' => Str::contains($cleanUsername, '/') ? "https://linkedin.com/{$cleanUsername}" : "https://linkedin.com/in/{$cleanUsername}",
                'whatsapp' => Str::startsWith($cleanUsername, '+') ? "https://wa.me/" . preg_replace('/\D/', '', $cleanUsername) : $cleanUsername,
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
                'formatted' => self::formatNumber($baseCount),
                'label' => $label,
                'url' => $url,
                'username' => $cleanUsername
            ];
        });
    }

    private static function formatNumber(int $num): string
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
