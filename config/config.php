<?php
declare(strict_types=1);

// Yerel geliştirme dosyası varsa onu yükle (Canlı sunucuda bu dosya bulunmaz)
$localConfig = [];
if (file_exists(__DIR__ . '/config.local.php')) {
    $localConfig = require __DIR__ . '/config.local.php';
}

$default = [
    'app' => [
        'name' => getenv('APP_NAME') ?: 'Kebapzade Premium',
        'base_url' => getenv('APP_URL') ?: '', // Boş bırakıldığında otomatik tespit edilir
        'timezone' => getenv('APP_TIMEZONE') ?: 'Europe/Istanbul',
        'session_name' => 'kebapzade_admin',
    ],
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => (int)(getenv('DB_PORT') ?: 3306),
        'name' => getenv('DB_NAME') ?: 'kebapzade_menu',
        'user' => getenv('DB_USER') ?: 'kebapzade',
        'pass' => getenv('DB_PASS') !== false ? (string)getenv('DB_PASS') : 'kebapzade123',
        'charset' => 'utf8mb4',
    ],
    'upload' => [
        'max_bytes' => 5 * 1024 * 1024,
        'menu_dir' => __DIR__ . '/../uploads/menu',
    ],
];

return array_replace_recursive($default, $localConfig);
