<?php
declare(strict_types=1);

function db(array $config): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    $d = $config['db'];

    if (($d['driver'] ?? '') === 'sqlite' || ($d['host'] ?? '') === 'sqlite') {
        $path = $d['path'] ?? __DIR__ . '/../database/local.sqlite';
        $pdo = new PDO('sqlite:' . $path, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        init_sqlite_schema($pdo);
        return $pdo;
    }

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
        // Yerel çalışma/test ortamı için kesintisiz SQLite yedeği
        $isLocal = PHP_SAPI === 'cli' || in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true) || str_contains($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1') || str_contains($_SERVER['HTTP_HOST'] ?? '', 'localhost');
        if ($isLocal) {
            $path = __DIR__ . '/../database/local.sqlite';
            $pdo = new PDO('sqlite:' . $path, null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            init_sqlite_schema($pdo);
            return $pdo;
        }

        http_response_code(500);
        $msg = 'Veritabanı bağlantısı kurulamadı. Sunucu yapılandırmasını kontrol edin.';
        $msg .= '<br><br><strong>Hata Detayı:</strong> ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        exit($msg);
    }
}

function init_sqlite_schema(PDO $pdo): void
{
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (`key` TEXT PRIMARY KEY, `value` TEXT)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (id INTEGER PRIMARY KEY AUTOINCREMENT, name_tr TEXT, name_en TEXT, slug TEXT UNIQUE, description_tr TEXT, description_en TEXT, image_path TEXT, sort_order INTEGER DEFAULT 0, is_active INTEGER DEFAULT 1)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS items (id INTEGER PRIMARY KEY AUTOINCREMENT, category_id INTEGER, name_tr TEXT, name_en TEXT, description_tr TEXT, description_en TEXT, price REAL, price_note_tr TEXT, price_note_en TEXT, image_path TEXT, is_active INTEGER DEFAULT 1, is_featured INTEGER DEFAULT 0, is_vegetarian INTEGER DEFAULT 0, is_spicy INTEGER DEFAULT 0, sort_order INTEGER DEFAULT 0)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS item_variants (id INTEGER PRIMARY KEY AUTOINCREMENT, item_id INTEGER, label_tr TEXT, label_en TEXT, price REAL, sort_order INTEGER DEFAULT 0)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS admins (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, email TEXT UNIQUE, password_hash TEXT)");

    // Seed if empty
    $count = (int)$pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    if ($count === 0 && file_exists(__DIR__ . '/../database/seed.php')) {
        $seed = require __DIR__ . '/../database/seed.php';
        $catMap = [];
        $stmtCat = $pdo->prepare("INSERT OR IGNORE INTO categories (slug, name_tr, name_en, sort_order, is_active) VALUES (?, ?, ?, ?, 1)");
        foreach ($seed['categories'] ?? [] as $c) {
            $stmtCat->execute([$c[0], $c[1], $c[2], $c[3]]);
            $catMap[$c[0]] = (int)$pdo->lastInsertId();
        }
        $stmtItem = $pdo->prepare("INSERT INTO items (category_id, name_tr, name_en, description_tr, description_en, is_featured, is_spicy, is_vegetarian, price, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 250.00, 10, 1)");
        foreach ($seed['items'] ?? [] as $i) {
            $catId = $catMap[$i[0]] ?? null;
            if ($catId) {
                $stmtItem->execute([$catId, $i[1], $i[2], $i[3], $i[4], $i[5], $i[6], $i[7]]);
            }
        }
    }
}

