<?php
declare(strict_types=1);

function admin_user(): ?array {
    return $_SESSION['admin_user'] ?? null;
}

function require_admin(): void {
    if (!admin_user()) redirect('/admin/login.php');
}

function login_locked(): bool {
    return (int)($_SESSION['login_locked_until'] ?? 0) > time();
}

function failed_login(): void {
    $n = (int)($_SESSION['login_attempts'] ?? 0) + 1;
    $_SESSION['login_attempts'] = $n;
    if ($n >= 5) {
        $_SESSION['login_locked_until'] = time() + 300;
        $_SESSION['login_attempts'] = 0;
    }
}

function login_admin(PDO $pdo, string $email, string $password): bool {
    $q = $pdo->prepare('SELECT id,name,email,password_hash FROM admins WHERE email = ? LIMIT 1');
    $q->execute([mb_strtolower(trim($email))]);
    $admin = $q->fetch();
    if (!$admin || !password_verify($password, $admin['password_hash'])) return false;

    session_regenerate_id(true);
    $_SESSION['admin_user'] = [
        'id' => (int)$admin['id'],
        'name' => $admin['name'],
        'email' => $admin['email'],
    ];
    unset($_SESSION['login_attempts'], $_SESSION['login_locked_until']);
    return true;
}

function logout_admin(): void {
    unset($_SESSION['admin_user']);
    session_regenerate_id(true);
}
