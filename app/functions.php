<?php
declare(strict_types=1);

function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function base_url(string $path = ''): string {
    global $config;

    // İç linklerde kök-göreceli (root-relative) yol döndürülür (/qr-menu.php vb.)
    // Böylece ziyaretçi hangi alan adındaysa (kebapzade.com) kesinlikle orada kalır,
    // üçüncü parti veya vercel.app uzantılı adresler linklerde asla görünmez.
    if ($path !== '') {
        return '/' . ltrim($path, '/');
    }

    $base = rtrim((string)($config['app']['base_url'] ?? ''), '/');
    if ($base === '') {
        $protoHeader = trim(explode(',', (string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''))[0] ?? '');
        $proto = strtolower($protoHeader) === 'https' || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $hostHeader = trim(explode(',', (string)($_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'] ?? ''))[0] ?? '');
        if ($hostHeader !== '' && preg_match('/^[A-Za-z0-9.-]+(?::[0-9]{1,5})?$/', $hostHeader)) {
            $base = $proto . '://' . $hostHeader;
        }
    }
    return $base;
}

function absolute_url(string $path = ''): string {
    global $config;
    $base = rtrim((string)($config['app']['base_url'] ?? ''), '/');
    if ($base === '') {
        $protoHeader = trim(explode(',', (string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''))[0] ?? '');
        $proto = strtolower($protoHeader) === 'https' || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $hostHeader = trim(explode(',', (string)($_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'] ?? ''))[0] ?? '');
        if ($hostHeader !== '' && preg_match('/^[A-Za-z0-9.-]+(?::[0-9]{1,5})?$/', $hostHeader)) {
            $base = $proto . '://' . $hostHeader;
        } else {
            $base = 'https://kebapzade.com';
        }
    }
    if ($path === '') return $base;
    return $base . '/' . ltrim($path, '/');
}

function redirect(string $path): never {
    header('Location: ' . base_url($path));
    exit;
}

function slugify(string $text): string {
    $map = ['ş'=>'s','Ş'=>'s','ı'=>'i','İ'=>'i','ç'=>'c','Ç'=>'c','ü'=>'u','Ü'=>'u','ö'=>'o','Ö'=>'o','ğ'=>'g','Ğ'=>'g'];
    $text = strtr(trim($text), $map);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    return trim($text, '-') ?: 'kategori';
}

function flash(string $key, ?string $set = null): ?string {
    if ($set !== null) {
        $_SESSION['_flash'][$key] = $set;
        return null;
    }
    $v = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $v;
}

function is_post(): bool {
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function checked(bool|int|string|null $v): string {
    return $v ? 'checked' : '';
}

function selected(string|int|null $a, string|int|null $b): string {
    return (string)$a === (string)$b ? 'selected' : '';
}

function money(?string $value): string {
    if ($value === null || $value === '' || !is_numeric($value)) return '';
    $n = (float)$value;
    $dec = floor($n) == $n ? 0 : 2;
    return number_format($n, $dec, ',', '.') . ' ₺';
}

function get_settings_cache_path(): string {
    $dir = sys_get_temp_dir() . '/kebapzade_cache';
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
    return $dir . '/settings_cache.json';
}

function setting(PDO $pdo, string $key, string $default = ''): string {
    static $cache = null;

    if ($cache === null) {
        $path = get_settings_cache_path();
        if (file_exists($path) && (time() - filemtime($path) < 600)) {
            $raw = @file_get_contents($path);
            if ($raw) {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $cache = $decoded;
                }
            }
        }

        if ($cache === null) {
            $cache = [];
            try {
                $rows = $pdo->query('SELECT `key`, `value` FROM settings')->fetchAll(PDO::FETCH_KEY_PAIR);
                if (is_array($rows)) {
                    $cache = $rows;
                    @file_put_contents($path, json_encode($cache, JSON_UNESCAPED_UNICODE));
                }
            } catch (Throwable $e) {
                // sessiz devam et
            }
        }
    }

    if (array_key_exists($key, $cache)) {
        return (string)$cache[$key];
    }

    return $default;
}

function get_menu_cache_path(): string {
    $dir = sys_get_temp_dir() . '/kebapzade_cache';
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
    return $dir . '/menu_cache.json';
}

function menu_cache_get(int $ttlSeconds = 600): ?array {
    $path = get_menu_cache_path();
    if (!file_exists($path)) {
        return null;
    }
    if ((time() - filemtime($path)) > $ttlSeconds) {
        return null;
    }
    $raw = @file_get_contents($path);
    if (!$raw) return null;
    $data = json_decode($raw, true);
    if (!is_array($data) || empty($data['categories'])) {
        return null;
    }
    return $data;
}

function menu_cache_set(array $data): void {
    $path = get_menu_cache_path();
    @file_put_contents($path, json_encode($data, JSON_UNESCAPED_UNICODE));
}

function clear_menu_cache(): void {
    $dir = sys_get_temp_dir() . '/kebapzade_cache';
    if (file_exists($dir . '/menu_cache.json')) {
        @unlink($dir . '/menu_cache.json');
    }
    if (file_exists($dir . '/settings_cache.json')) {
        @unlink($dir . '/settings_cache.json');
    }
}

function lang(): string {
    if (isset($_GET['lang'])) {
        $l = $_GET['lang'] === 'en' ? 'en' : 'tr';
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['site_lang'] = $l;
        }
        if (!headers_sent()) {
            setcookie('site_lang', $l, [
                'expires' => time() + 86400 * 30,
                'path' => '/',
                'httponly' => false,
                'samesite' => 'Lax'
            ]);
        }
        return $l;
    }
    return ($_COOKIE['site_lang'] ?? $_SESSION['site_lang'] ?? 'tr') === 'en' ? 'en' : 'tr';
}

function image_url(?string $path): string {
    if (!$path) return '';
    if (preg_match('~^https?://~i', $path)) return $path;
    return base_url($path);
}

function ensure_grill_and_kebab_merged(PDO $pdo): void
{
    static $done = false;
    if ($done) return;
    $done = true;

    try {
        $q = $pdo->query("SELECT id, slug, name_tr FROM categories WHERE slug IN ('izgaralar', 'kebaplar', 'izgara-kebaplar')");
        $cats = $q ? $q->fetchAll() : [];
        if (!$cats) return;

        $catMap = [];
        foreach ($cats as $c) {
            $catMap[$c['slug']] = (int)$c['id'];
        }

        // Hem izgaralar hem kebaplar varsa ikisini birleştir
        if (isset($catMap['izgaralar']) && isset($catMap['kebaplar'])) {
            $targetId = $catMap['izgaralar'];
            $otherId = $catMap['kebaplar'];

            // Kebaplar kategorisindeki tüm ürünleri tek kategoriye aktar
            $updItems = $pdo->prepare("UPDATE items SET category_id = ? WHERE category_id = ?");
            $updItems->execute([$targetId, $otherId]);

            // Kategori başlığını 'Izgara & Kebaplar' olarak güncelle
            $updCat = $pdo->prepare("UPDATE categories SET name_tr = 'Izgara & Kebaplar', name_en = 'Grills & Kebabs', slug = 'izgara-kebaplar' WHERE id = ?");
            $updCat->execute([$targetId]);

            // Artık boş olan diğer kategoriyi kaldır
            $delCat = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $delCat->execute([$otherId]);
        } elseif (isset($catMap['izgaralar']) && !isset($catMap['kebaplar']) && !isset($catMap['izgara-kebaplar'])) {
            $updCat = $pdo->prepare("UPDATE categories SET name_tr = 'Izgara & Kebaplar', name_en = 'Grills & Kebabs', slug = 'izgara-kebaplar' WHERE id = ?");
            $updCat->execute([$catMap['izgaralar']]);
        } elseif (!isset($catMap['izgaralar']) && isset($catMap['kebaplar']) && !isset($catMap['izgara-kebaplar'])) {
            $updCat = $pdo->prepare("UPDATE categories SET name_tr = 'Izgara & Kebaplar', name_en = 'Grills & Kebabs', slug = 'izgara-kebaplar' WHERE id = ?");
            $updCat->execute([$catMap['kebaplar']]);
        }
    } catch (Throwable $e) {
        // Hata durumunda sessizce geç
    }
}

