<?php
declare(strict_types=1);
require_once __DIR__ . '/app/images.php';

$id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$width = filter_var($_GET['w'] ?? 0, FILTER_VALIDATE_INT);
if (!$id || !in_array($width, [0, 320, 640, 960, 1280], true)) {
    http_response_code(404);
    exit;
}

$cacheDir = sys_get_temp_dir() . '/kebapzade_images_' . substr(hash('sha256', __DIR__), 0, 12);
$cachePath = $cacheDir . '/' . $id . '-' . $width . '-v1.bin';
$body = $width > 0 && is_file($cachePath) ? @file_get_contents($cachePath) : false;
$mime = $body !== false ? (string)(new finfo(FILEINFO_MIME_TYPE))->buffer($body) : '';

if ($body === false) {
    $config = require __DIR__ . '/config/config.php';
    require_once __DIR__ . '/app/db.php';
    $pdo = db($config);
    try {
        $q = $pdo->prepare('SELECT mime_type, data FROM media_uploads WHERE id=? LIMIT 1');
        $q->execute([$id]);
        $row = $q->fetch();
    } catch (Throwable) {
        $row = false;
    }
    if (!$row) { http_response_code(404); exit; }

    $body = (string)$row['data'];
    $mime = (string)$row['mime_type'];
    if ($width > 0) {
        $variant = optimized_image($body, $width);
        $body = $variant['body'];
        $mime = $variant['mime'];
        if (!is_dir($cacheDir)) @mkdir($cacheDir, 0700, true);
        $tempPath = $cachePath . '.' . bin2hex(random_bytes(6)) . '.tmp';
        if (@file_put_contents($tempPath, $body, LOCK_EX) !== false) {
            if (!@rename($tempPath, $cachePath)) @unlink($tempPath);
        }
    }
}

$etag = '"dbimg-' . $id . '-' . $width . '-' . sha1($body) . '"';
header('Content-Type: ' . $mime);
header('Cache-Control: public, max-age=31536000, s-maxage=31536000, immutable');
header('ETag: ' . $etag);
header('X-Content-Type-Options: nosniff');
if (trim((string)($_SERVER['HTTP_IF_NONE_MATCH'] ?? '')) === $etag) {
    http_response_code(304);
    exit;
}
header('Content-Length: ' . strlen($body));
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'HEAD') echo $body;
