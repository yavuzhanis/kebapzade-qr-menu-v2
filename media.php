<?php
declare(strict_types=1);

$config = require __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/db.php';
$pdo = db($config);

$id = max(0, (int)($_GET['id'] ?? 0));
if ($id <= 0) {
    http_response_code(404);
    exit;
}

try {
    $q = $pdo->prepare('SELECT mime_type, size_bytes, data, created_at FROM media_uploads WHERE id=? LIMIT 1');
    $q->execute([$id]);
    $row = $q->fetch();
} catch (Throwable) {
    $row = false;
}

if (!$row) {
    http_response_code(404);
    exit;
}

$body = (string)$row['data'];
$etag = '"dbimg-' . $id . '-' . sha1($body) . '"';
if (trim((string)($_SERVER['HTTP_IF_NONE_MATCH'] ?? '')) === $etag) {
    http_response_code(304);
    exit;
}

header('Content-Type: ' . (string)$row['mime_type']);
header('Content-Length: ' . strlen($body));
header('Cache-Control: public, max-age=31536000, immutable');
header('ETag: ' . $etag);
header('X-Content-Type-Options: nosniff');
echo $body;
