<?php
declare(strict_types=1);

function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function base_url(string $path = ''): string {
    global $config;
    $base = rtrim((string)($config['app']['base_url'] ?? ''), '/');
    if ($path === '') return $base ?: '';
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

function setting(PDO $pdo, string $key, string $default = ''): string {
    static $cache = [];
    if (array_key_exists($key, $cache)) return (string)$cache[$key];

    try {
        $q = $pdo->prepare('SELECT value FROM settings WHERE `key` = ? LIMIT 1');
        $q->execute([$key]);
        $v = $q->fetchColumn();
        $cache[$key] = $v === false ? $default : (string)$v;
        return (string)$cache[$key];
    } catch (Throwable $e) {
        return $default;
    }
}

function lang(): string {
    if (isset($_GET['lang'])) {
        $_SESSION['site_lang'] = $_GET['lang'] === 'en' ? 'en' : 'tr';
    }
    return ($_SESSION['site_lang'] ?? 'tr') === 'en' ? 'en' : 'tr';
}

function image_url(?string $path): string {
    if (!$path) return '';
    if (preg_match('~^https?://~i', $path)) return $path;
    return base_url($path);
}
