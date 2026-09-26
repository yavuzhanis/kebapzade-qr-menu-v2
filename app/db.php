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

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => (int)($d['timeout'] ?? 5),
    ];

    $sslEnabled = (bool)($d['ssl'] ?? false);
    $sslCa = trim((string)($d['ssl_ca'] ?? ''));
    if ($sslEnabled && defined('PDO::MYSQL_ATTR_SSL_CA')) {
        if ($sslCa !== '') {
            $options[PDO::MYSQL_ATTR_SSL_CA] = $sslCa;
        } elseif (file_exists('/etc/ssl/certs/ca-certificates.crt')) {
            $options[PDO::MYSQL_ATTR_SSL_CA] = '/etc/ssl/certs/ca-certificates.crt';
        } elseif (file_exists('/etc/ssl/cert.pem')) {
            $options[PDO::MYSQL_ATTR_SSL_CA] = '/etc/ssl/cert.pem';
        }
    }
    if ($sslEnabled && defined('PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT')) {
        $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = (bool)($d['ssl_verify_server_cert'] ?? true);
    }

    try {
        $pdo = new PDO($dsn, (string)$d['user'], (string)$d['pass'], $options);
        return $pdo;
    } catch (Throwable $e) {
        http_response_code(500);
        $msg = 'Veritabanı bağlantısı kurulamadı. Sunucu yapılandırmasını kontrol edin.';
        if (($config['app']['debug'] ?? false) === true) {
            $msg .= '<br><br><strong>Hata Detayı:</strong> ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
        exit($msg);
    }
}
