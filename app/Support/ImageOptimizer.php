<?php

namespace App\Support;

class ImageOptimizer
{
    /**
     * Return an optimized WebP image URL with specific dimensions and quality compression.
     */
    public static function url(?string $url, int $width = 600, int $height = 400, int $quality = 75): string
    {
        if (empty($url)) {
            return "https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&q={$quality}&w={$width}&h={$height}&fm=webp";
        }

        // Unsplash optimization
        if (str_contains($url, 'images.unsplash.com')) {
            $base = preg_replace('/\?.*$/', '', $url);
            return "{$base}?auto=format&fit=crop&q={$quality}&w={$width}&h={$height}&fm=webp";
        }

        // Local storage file optimization
        if (str_starts_with($url, '/storage/') || str_contains($url, '/storage/')) {
            $relativePath = ltrim(parse_url($url, PHP_URL_PATH), '/');
            $fullPath = public_path($relativePath);

            if (!file_exists($fullPath) || !is_file($fullPath)) {
                // If local file is missing or inaccessible, return CDN fallback to prevent browser timeout (ERR_TIMED_OUT)
                return "https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&q={$quality}&w={$width}&h={$height}&fm=webp";
            }

            $cacheDir = public_path('storage/cache');
            if (!file_exists($cacheDir)) {
                @mkdir($cacheDir, 0755, true);
            }

            $filename = md5($relativePath . "_{$width}_{$height}_{$quality}") . '.webp';
            $cachedFile = $cacheDir . '/' . $filename;
            $cachedUrl = '/storage/cache/' . $filename;

            if (file_exists($cachedFile)) {
                return $cachedUrl;
            }

            // Generate WebP thumbnail using GD
            try {
                $info = @getimagesize($fullPath);
                if ($info) {
                    $mime = $info['mime'];
                    $srcImg = match ($mime) {
                        'image/jpeg' => @imagecreatefromjpeg($fullPath),
                        'image/png' => @imagecreatefrompng($fullPath),
                        'image/webp' => @imagecreatefromwebp($fullPath),
                        default => null
                    };

                    if ($srcImg) {
                        $origW = imagesx($srcImg);
                        $origH = imagesy($srcImg);

                        $targetRatio = $width / $height;
                        $origRatio = $origW / $origH;

                        if ($origRatio > $targetRatio) {
                            $cropW = (int) ($origH * $targetRatio);
                            $cropH = $origH;
                            $srcX = (int) (($origW - $cropW) / 2);
                            $srcY = 0;
                        } else {
                            $cropW = $origW;
                            $cropH = (int) ($origW / $targetRatio);
                            $srcX = 0;
                            $srcY = (int) (($origH - $cropH) / 2);
                        }

                        $dstImg = imagecreatetruecolor($width, $height);
                        imagealphablending($dstImg, false);
                        imagesavealpha($dstImg, true);

                        imagecopyresampled($dstImg, $srcImg, 0, 0, $srcX, $srcY, $width, $height, $cropW, $cropH);
                        imagewebp($dstImg, $cachedFile, $quality);

                        imagedestroy($srcImg);
                        imagedestroy($dstImg);

                        if (file_exists($cachedFile)) {
                            return $cachedUrl;
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Fallback to original URL
            }
        }

        return $url;
    }
}
