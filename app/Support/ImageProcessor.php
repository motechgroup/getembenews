<?php

namespace App\Support;

class ImageProcessor
{
    /**
     * Process the image: apply watermark and compress/resize if size > 1MB.
     *
     * @param string $path Absolute path to the file.
     * @param bool $applyWatermark Whether to draw a watermark text.
     * @return bool
     */
    public static function process(string $path, bool $applyWatermark = true): bool
    {
        if (!file_exists($path)) {
            return false;
        }

        // Get image details
        $info = @getimagesize($path);
        if (!$info) {
            return false; // Not a valid image
        }

        $mime = $info['mime'];
        $width = $info[0];
        $height = $info[1];

        // Only process standard formats
        if (!in_array($mime, ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])) {
            return false;
        }

        // Create image resource based on mime type
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = @imagecreatefromjpeg($path);
                break;
            case 'image/png':
                $image = @imagecreatefrompng($path);
                break;
            case 'image/webp':
                $image = @imagecreatefromwebp($path);
                break;
            default:
                $image = false;
        }

        if (!$image) {
            return false;
        }

        // 1. Resize down if extremely large to save storage
        $maxDimension = 2000;
        if ($width > $maxDimension || $height > $maxDimension) {
            if ($width > $height) {
                $newWidth = $maxDimension;
                $newHeight = (int) ($height * ($maxDimension / $width));
            } else {
                $newHeight = $maxDimension;
                $newWidth = (int) ($width * ($maxDimension / $height));
            }

            $scaledImage = @imagescale($image, $newWidth, $newHeight);
            if ($scaledImage) {
                imagedestroy($image);
                $image = $scaledImage;
                $width = $newWidth;
                $height = $newHeight;
            }
        }

        // 2. Apply Watermark ("getembe digital")
        if ($applyWatermark) {
            $watermarkText = 'getembe digital';
            $fontSize = 5; // Built-in font size (1 to 5)
            $fontWidth = imagefontwidth($fontSize);
            $fontHeight = imagefontheight($fontSize);

            $textWidth = strlen($watermarkText) * $fontWidth;

            // Position: Bottom-right corner with 15px padding
            $padding = 15;
            $x = $width - $textWidth - $padding;
            $y = $height - $fontHeight - $padding;

            if ($x > 0 && $y > 0) {
                // Allocate colors
                $white = imagecolorallocate($image, 255, 255, 255);
                $black = imagecolorallocate($image, 0, 0, 0);

                if ($white !== false && $black !== false) {
                    // Draw a subtle shadow first
                    imagestring($image, $fontSize, $x + 1, $y + 1, $watermarkText, $black);
                    // Draw main white text
                    imagestring($image, $fontSize, $x, $y, $watermarkText, $white);
                }
            }
        }

        // 3. Compress if file size > 1MB
        $fileSize = filesize($path);
        $maxBytes = 1 * 1024 * 1024; // 1MB
        $shouldCompress = $fileSize > $maxBytes;

        // Save image back
        $success = false;
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                // For JPEG, compress by setting quality to 75 if > 1MB, otherwise 90
                $quality = $shouldCompress ? 75 : 90;
                $success = @imagejpeg($image, $path, $quality);
                break;
            case 'image/png':
                // For PNG, compress by setting compression level to 7 (0-9) if > 1MB, otherwise 4
                $quality = $shouldCompress ? 7 : 4;
                $success = @imagepng($image, $path, $quality);
                break;
            case 'image/webp':
                // For WebP, compress by setting quality to 75 if > 1MB, otherwise 85
                $quality = $shouldCompress ? 75 : 85;
                $success = @imagewebp($image, $path, $quality);
                break;
        }

        // Free up memory
        imagedestroy($image);

        return $success;
    }
}
