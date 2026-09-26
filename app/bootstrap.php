<?php
declare(strict_types=1);

$config = require __DIR__ . '/../config/config.php';
date_default_timezone_set($config['app']['timezone'] ?? 'Europe/Istanbul');

require_once __DIR__ . '/db.php';
$pdo = db($config);

if (session_status() !== PHP_SESSION_ACTIVE) {
    $sessionConfig = $config['session'] ?? [];
    $driver = (string)($sessionConfig['driver'] ?? 'database');

    if ($driver === 'database') {
        require_once __DIR__ . '/database-session-handler.php';
        $handler = new DatabaseSessionHandler(
            $pdo,
            (int)($sessionConfig['lifetime'] ?? 7200),
            (string)($sessionConfig['table'] ?? 'app_sessions'),
            (bool)($sessionConfig['auto_migrate'] ?? true),
        );
        session_set_save_handler($handler, true);
    }

    $forwardedProto = strtolower(trim(explode(',', (string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''))[0] ?? ''));
    $requestIsHttps = $forwardedProto === 'https' || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    $secureOverride = $sessionConfig['secure_cookie'] ?? null;
    $secureCookie = $secureOverride === null ? $requestIsHttps : (bool)$secureOverride;

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.gc_maxlifetime', (string)($sessionConfig['lifetime'] ?? 7200));

    session_name($config['app']['session_name'] ?? 'kebapzade_admin');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'secure' => $secureCookie,
        'samesite' => 'Lax',
    ]);
    session_start();
}

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/auth.php';
