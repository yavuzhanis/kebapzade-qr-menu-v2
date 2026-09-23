<?php
require __DIR__ . '/../app/bootstrap.php';
if (admin_user()) redirect('/admin/index.php');

$error='';
if (is_post()) {
    verify_csrf();
    if (login_locked()) {
        $error='Çok fazla hatalı deneme. 5 dakika sonra tekrar deneyin.';
    } else {
        $email=(string)($_POST['email'] ?? '');
        $password=(string)($_POST['password'] ?? '');
        if (login_admin($pdo,$email,$password)) redirect('/admin/index.php');
        failed_login();
        $error='E-posta veya şifre hatalı.';
    }
}
?>
<!doctype html><html lang="tr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Kebapzade Yönetim</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?=e(base_url('/assets/css/admin.css?v=' . filemtime(__DIR__ . '/../assets/css/admin.css')))?>">
<script>
  (function() {
    var theme = localStorage.getItem('kebapzade_admin_theme') || 'light';
    document.documentElement.setAttribute('data-theme', theme);
  })();
</script>
</head>
<body class="login-page">
<form class="login" method="post">
 <?=csrf_field()?>
 <h1>Yönetim Paneli</h1>
 <p>Menüyü, fiyatları, ürün fotoğraflarını ve site iletişim bilgilerini buradan yönetin.</p>
 <?php if($error): ?><div class="notice err"><?=e($error)?></div><?php endif;?>
 <label>E-posta<input class="input" type="email" name="email" required autofocus></label>
 <label>Şifre<input class="input" type="password" name="password" required></label>
 <button class="btn primary" style="width:100%">Giriş Yap</button>
</form>
</body></html>
