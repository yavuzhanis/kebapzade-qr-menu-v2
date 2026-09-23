<?php
declare(strict_types=1);

function db(array $config): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    $d = $config['db'];
    $port = !empty($d['port']) ? (int)$d['port'] : 3306;
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $d['host'],
        $port,
        $d['name'],
        $d['charset'] ?? 'utf8mb4'
    );

    try {
        $pdo = new PDO($dsn, $d['user'], $d['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return $pdo;
    } catch (Throwable $e) {
        http_response_code(500);
        $msg = 'Veritabanı bağlantısı kurulamadı. Lütfen config/config.php ayarlarınızı kontrol edin.';
        if (isset($_GET['debug_db'])) {
            $msg .= '<br><br><strong>Hata Detayı:</strong> ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
        exit($msg);
    }
}
