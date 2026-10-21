<?php
declare(strict_types=1);

// Versioned URLs keep existing originals available while browsers cache smaller copies.
function image_variant_url(?string $path, int $width = 640): string {
    $url = image_url($path);
    if ($url === '') return '';
    $parts = parse_url($url);
    if (($parts['path'] ?? '') !== '/media.php') return $url;
    parse_str($parts['query'] ?? '', $query);
    if (empty($query['id'])) return $url;
    $query['w'] = in_array($width, [320, 640, 960, 1280], true) ? $width : 640;
    $query['v'] = 1;
    return base_url('/media.php?' . http_build_query($query));
}

/** Resize without cropping or upscaling; preserve PNG transparency and JPEG orientation. */
function optimized_image(string $body, int $maxWidth): array {
    $info = @getimagesizefromstring($body);
    $mime = $info['mime'] ?? 'application/octet-stream';
    $original = ['body' => $body, 'mime' => $mime];
    if (!$info || !function_exists('imagewebp') || $info[0] * $info[1] > 24000000) return $original;
    $source = @imagecreatefromstring($body);
    if (!$source) return $original;

    try {
        if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
            $stream = fopen('php://temp', 'w+b');
            if ($stream) {
                fwrite($stream, $body);
                rewind($stream);
                $exif = @exif_read_data($stream);
                fclose($stream);
                $orientation = (int)($exif['Orientation'] ?? 1);
                $angle = match ($orientation) { 3, 4 => 180, 5, 6 => -90, 7, 8 => 90, default => 0 };
                if ($angle) {
                    $rotated = imagerotate($source, $angle, 0);
                    if ($rotated) { imagedestroy($source); $source = $rotated; }
                }
                if (in_array($orientation, [2, 4, 5, 7], true)) imageflip($source, IMG_FLIP_HORIZONTAL);
            }
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $scale = min(1, max(1, $maxWidth) / max($width, $height));
        $targetWidth = max(1, (int)round($width * $scale));
        $targetHeight = max(1, (int)round($height * $scale));
        $target = imagecreatetruecolor($targetWidth, $targetHeight);
        if (!$target) return $original;
        try {
            imagealphablending($target, false);
            imagesavealpha($target, true);
            imagefill($target, 0, 0, imagecolorallocatealpha($target, 0, 0, 0, 127));
            imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
            ob_start();
            $ok = imagewebp($target, null, 74);
            $output = ob_get_clean();
            if (!$ok || !$output) return $original;
            if ($scale >= 1 && strlen($output) >= strlen($body)) return $original;
            return ['body' => $output, 'mime' => 'image/webp'];
        } finally {
            imagedestroy($target);
        }
    } finally {
        imagedestroy($source);
    }
}
