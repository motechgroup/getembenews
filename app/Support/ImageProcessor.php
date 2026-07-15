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
            $fontSize = 4; // Built-in font size (1 to 5)
            $fontWidth = imagefontwidth($fontSize);
            $fontHeight = imagefontheight($fontSize);

            $textWidth = strlen($watermarkText) * $fontWidth;

            // Position: Bottom-left corner with 20px padding
            $padding = 20;
            $x = $padding;
            $y = $height - $fontHeight - $padding;

            // Bounding box for the glass-embedded backing panel
            $boxPaddingX = 10;
            $boxPaddingY = 6;
            $boxLeft = $x - $boxPaddingX;
            $boxTop = $y - $boxPaddingY;
            $boxRight = $x + $textWidth + $boxPaddingX;
            $boxBottom = $y + $fontHeight + $boxPaddingY;

            if ($boxLeft > 0 && $boxBottom < $height && $boxRight < $width && $boxTop > 0) {
                // Set alpha blending to blend drawing colors with background image
                imagealphablending($image, true);

                // Allocate glass-morphic colors:
                // 1. Semi-transparent dark backing card (black with alpha 80 out of 127)
                $glassBg = imagecolorallocatealpha($image, 0, 0, 0, 80);
                
                // 2. Semi-transparent white card border/highlight (white with alpha 95 out of 127)
                $glassBorder = imagecolorallocatealpha($image, 255, 255, 255, 95);
                
                // 3. Bright solid white text (white with alpha 0)
                $textColor = imagecolorallocatealpha($image, 255, 255, 255, 0);

                if ($glassBg !== false && $glassBorder !== false && $textColor !== false) {
                    // Draw backing card filled rectangle
                    imagefilledrectangle($image, $boxLeft, $boxTop, $boxRight, $boxBottom, $glassBg);
                    
                    // Draw outer border (1px border line)
                    imagerectangle($image, $boxLeft, $boxTop, $boxRight, $boxBottom, $glassBorder);
                    
                    // Draw dropshadowed text first (semi-transparent black)
                    $shadowColor = imagecolorallocatealpha($image, 0, 0, 0, 60);
                    if ($shadowColor !== false) {
                        imagestring($image, $fontSize, $x + 1, $y + 1, $watermarkText, $shadowColor);
                    }

                    // Write main white text inside the glass panel
                    imagestring($image, $fontSize, $x, $y, $watermarkText, $textColor);
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
