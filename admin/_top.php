<?php
$pageTitle = $pageTitle ?? 'Yönetim';
$active = $active ?? '';
$me = admin_user();
?>
<!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=e($pageTitle)?> | Kebapzade</title>
<link rel="stylesheet" href="<?=e(base_url('/assets/css/admin.css?v=' . filemtime(__DIR__ . '/../assets/css/admin.css')))?>">
<script>
  (function() {
    var theme = localStorage.getItem('kebapzade_admin_theme') || 'light';
    document.documentElement.setAttribute('data-theme', theme);
  })();
</script>
</head>
<body>
<div class="admin">
<aside class="side">
 <div class="logo-wrap">
   <div class="logo">Kebapzade</div>
   <button type="button" class="admin-theme-toggle" data-admin-theme-toggle title="Aydınlık / Karanlık Mod">
     <span class="theme-icon-sun" aria-hidden="true">☀️</span>
     <span class="theme-icon-moon" aria-hidden="true">🌙</span>
   </button>
 </div>
 <nav>
  <a class="<?=$active==='dashboard'?'active':''?>" href="<?=e(base_url('/admin/index.php'))?>">Genel Bakış</a>
  <a class="<?=$active==='items'?'active':''?>" href="<?=e(base_url('/admin/items.php'))?>">Ürünler</a>
  <a class="<?=$active==='categories'?'active':''?>" href="<?=e(base_url('/admin/categories.php'))?>">Kategoriler</a>
  <a class="<?=$active==='settings'?'active':''?>" href="<?=e(base_url('/admin/settings.php'))?>">Site Ayarları</a>
  <a class="<?=$active==='users'?'active':''?>" href="<?=e(base_url('/admin/users.php'))?>">Yöneticiler</a>
  <a class="<?=$active==='qr-menu'?'active':''?>" href="<?=e(base_url('/admin/qr-menu.php'))?>">Masa QR Baskı</a>
  <a target="_blank" href="<?=e(base_url('/'))?>">Siteyi Gör ↗</a>
 </nav>
 <a class="logout" href="<?=e(base_url('/admin/logout.php'))?>">Çıkış Yap · <?=e($me['name'] ?? '')?></a>
</aside>
<main class="main">
<?php if($msg=flash('ok')): ?><div class="notice ok"><?=e($msg)?></div><?php endif;?>
<?php if($msg=flash('err')): ?><div class="notice err"><?=e($msg)?></div><?php endif;?>
