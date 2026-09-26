<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
$out = [
    'status' => 'ok',
    'php' => PHP_VERSION,
    'extensions' => [
        'pdo_mysql' => extension_loaded('pdo_mysql'),
        'fileinfo' => extension_loaded('fileinfo'),
        'mbstring' => extension_loaded('mbstring'),
        'curl' => extension_loaded('curl'),
    ],
];
echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
