<?php
declare(strict_types=1);

// Yerel geliştirme dosyası varsa yükle. Canlı sunucuda bu dosyayı kullanmayın.
$localConfig = [];
if (file_exists(__DIR__ . '/config.local.php')) {
    $localConfig = require __DIR__ . '/config.local.php';
}

$envBool = static function (string $name, bool $default = false): bool {
    $value = getenv($name);
    if ($value === false || $value === '') return $default;
    return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? $default;
};

// Vercel/managed DB servislerinde sık kullanılan DATABASE_URL / MYSQL_URL desteği.
$dbUrl = (string)(getenv('DATABASE_URL') ?: getenv('MYSQL_URL') ?: '');
$urlDb = [];
if ($dbUrl !== '') {
    $parsed = parse_url($dbUrl);
    if (is_array($parsed)) {
        $urlDb = [
            'host' => isset($parsed['host']) ? urldecode((string)$parsed['host']) : null,
            'port' => isset($parsed['port']) ? (int)$parsed['port'] : null,
            'name' => isset($parsed['path']) ? urldecode(ltrim((string)$parsed['path'], '/')) : null,
            'user' => isset($parsed['user']) ? urldecode((string)$parsed['user']) : null,
            'pass' => isset($parsed['pass']) ? urldecode((string)$parsed['pass']) : null,
        ];
    }
}

$blobToken = (string)(getenv('BLOB_READ_WRITE_TOKEN') ?: '');
$blobOidcToken = (string)(getenv('VERCEL_OIDC_TOKEN') ?: '');
$blobStoreId = (string)(getenv('BLOB_STORE_ID') ?: '');
$storageDefault = ($blobToken !== '' || ($blobOidcToken !== '' && $blobStoreId !== '')) ? 'vercel_blob' : 'local';

$default = [
    'app' => [
        'name' => getenv('APP_NAME') ?: 'Kebapzade Premium',
        'base_url' => getenv('APP_URL') ?: '',
        'timezone' => getenv('APP_TIMEZONE') ?: 'Europe/Istanbul',
        'debug' => $envBool('APP_DEBUG', false),
        'session_name' => getenv('SESSION_NAME') ?: 'kebapzade_admin',
    ],
    'db' => [
        'host' => getenv('DB_HOST') ?: ($urlDb['host'] ?? '127.0.0.1'),
        'port' => (int)(getenv('DB_PORT') ?: ($urlDb['port'] ?? 3306)),
        'name' => getenv('DB_NAME') ?: ($urlDb['name'] ?? 'kebapzade_menu'),
        'user' => getenv('DB_USER') ?: ($urlDb['user'] ?? 'kebapzade'),
        'pass' => getenv('DB_PASS') !== false ? (string)getenv('DB_PASS') : (string)($urlDb['pass'] ?? ''),
        'ssl' => $envBool('DB_SSL', false)
            || (getenv('DB_SSL_CA') ?: '') !== ''
            || (isset($urlDb['host']) && (str_contains((string)$urlDb['host'], 'tidbcloud.com') || str_contains((string)$urlDb['host'], 'aivencloud.com'))),
        'ssl_ca' => getenv('DB_SSL_CA') ?: '',
        'ssl_verify_server_cert' => $envBool('DB_SSL_VERIFY_SERVER_CERT', true),
        'timeout' => max(1, (int)(getenv('DB_TIMEOUT') ?: 5)),
    ],
    'session' => [
        'driver' => getenv('SESSION_DRIVER') ?: 'database',
        'table' => getenv('SESSION_TABLE') ?: 'app_sessions',
        'lifetime' => max(900, (int)(getenv('SESSION_LIFETIME') ?: 7200)),
        'auto_migrate' => $envBool('SESSION_AUTO_MIGRATE', true),
        'secure_cookie' => getenv('SESSION_SECURE_COOKIE') === false
            ? null
            : $envBool('SESSION_SECURE_COOKIE', true),
    ],
    'upload' => [
        // Vercel Function request body limiti için güvenli pay bırakılır.
        'max_bytes' => max(262144, (int)(getenv('UPLOAD_MAX_BYTES') ?: 4 * 1024 * 1024)),
        'menu_dir' => __DIR__ . '/../uploads/menu',
        'site_dir' => __DIR__ . '/../uploads/site',
    ],
    'storage' => [
        // Not: 'STORAGE_DRIVER' Buildah/Vercel container tarafından rezerve edildiği için APP_STORAGE_DRIVER kullanılır.
        'driver' => getenv('APP_STORAGE_DRIVER') ?: $storageDefault,
        'blob_api_url' => rtrim((string)(getenv('VERCEL_BLOB_API_URL') ?: 'https://vercel.com/api/blob'), '/'),
        'blob_token' => $blobToken,
        'blob_oidc_token' => $blobOidcToken,
        'blob_store_id' => $blobStoreId,
    ],
];

return array_replace_recursive($default, $localConfig);
