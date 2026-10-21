<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../app/functions.php';

function check(bool $condition, string $message): void {
    if (!$condition) throw new RuntimeException($message);
}

check(function_exists('imagewebp'), 'GD with WebP support is required.');
$source = imagecreatetruecolor(1200, 900);
imagefilledrectangle($source, 0, 0, 1199, 899, imagecolorallocate($source, 150, 80, 30));
ob_start();
imagepng($source);
$png = (string)ob_get_clean();
imagedestroy($source);
$variant = optimized_image($png, 640);
$dimensions = getimagesizefromstring($variant['body']);
check($dimensions[0] === 640 && $dimensions[1] === 480, 'Resize must preserve the aspect ratio.');
check($variant['mime'] === 'image/webp', 'Photographs must use WebP.');
check(strlen($variant['body']) < strlen($png), 'A smaller photo must use fewer bytes.');

$small = imagecreatetruecolor(24, 12);
imagealphablending($small, false);
imagesavealpha($small, true);
imagefill($small, 0, 0, imagecolorallocatealpha($small, 0, 0, 0, 127));
imagesetpixel($small, 12, 6, imagecolorallocatealpha($small, 255, 0, 0, 0));
ob_start();
imagepng($small);
$transparent = (string)ob_get_clean();
imagedestroy($small);
$variant = optimized_image($transparent, 640);
$result = imagecreatefromstring($variant['body']);
check(imagesx($result) === 24 && imagesy($result) === 12, 'Small images must not be upscaled.');
$color = imagecolorsforindex($result, imagecolorat($result, 0, 0));
check($color['alpha'] === 127, 'Transparent logo backgrounds must stay transparent.');
imagedestroy($result);

check(image_variant_url('/media.php?id=42', 640) === '/media.php?id=42&w=640&v=1', 'Existing media URLs must request smaller versions.');
check(image_variant_url('/assets/images/hero.jpg', 640) === '/assets/images/hero.jpg', 'Static asset URLs must remain valid.');
check(image_variant_url(null, 640) === '', 'Missing images must retain their fallback behavior.');
check(optimized_image('invalid image', 640)['body'] === 'invalid image', 'Undecodable originals must not become empty responses.');
echo "Image resize, compression, transparency and URL checks passed.\n";
