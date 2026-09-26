<?php
declare(strict_types=1);
$dbStatus = 'unknown';
try {
    require_once __DIR__ . '/app/bootstrap.php';
    $categories = db($config)->query("SELECT count(*) FROM categories")->fetchColumn();
    $dbStatus = 'connected (' . $categories . ' categories)';
} catch (Throwable $e) {
    $dbStatus = 'error: ' . $e->getMessage();
}

$out = [
    'status' => 'ok',
    'php' => PHP_VERSION,
    'db' => $dbStatus,
    'extensions' => [
        'pdo_mysql' => extension_loaded('pdo_mysql'),
        'fileinfo' => extension_loaded('fileinfo'),
        'mbstring' => extension_loaded('mbstring'),
        'curl' => extension_loaded('curl'),
    ],
];
echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
