<?php
declare(strict_types=1);

// Yerel geliştirme dosyası varsa onu yükle (Canlı sunucuda bu dosya bulunmaz)
$localConfig = [];
if (file_exists(__DIR__ . '/config.local.php')) {
    $localConfig = require __DIR__ . '/config.local.php';
}

$default = [
    'app' => [
        'name' => 'Kebapzade Premium',
        'base_url' => '', // Boş bırakıldığında canlı alan adı (kebabzadeqr.freehosting.dev) otomatik tespit edilir
        'timezone' => 'Europe/Istanbul',
        'session_name' => 'kebapzade_admin',
    ],
    'db' => [
        // InfinityFree Canlı MySQL Bilgileri
        'host' => 'sql304.infinityfree.com',
        'port' => 3306,
        'name' => 'if0_42983387_menu',
        'user' => 'if0_42983387',
        'pass' => 'magevS4uihhv',
        'charset' => 'utf8mb4',
    ],
    'upload' => [
        'max_bytes' => 5 * 1024 * 1024,
        'menu_dir' => __DIR__ . '/../uploads/menu',
    ],
];

return array_replace_recursive($default, $localConfig);
