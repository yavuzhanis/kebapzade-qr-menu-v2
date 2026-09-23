<?php
declare(strict_types=1);
require __DIR__ . '/app/bootstrap.php';

$L = lang();
$restaurant = setting($pdo, 'restaurant_name', 'Kapadokya Kebapzade Restaurant');
$logo = image_url(setting($pdo, 'logo_image', '/assets/images/logo.svg'));
if (!$logo) {
    $logo = base_url('/assets/images/logo.svg');
}
$welcomeImage = image_url(setting($pdo, 'hero_image', '/assets/images/hero.jpg'));
if (!$welcomeImage) {
    $welcomeImage = base_url('/assets/images/hero.jpg');
}

$address = setting($pdo, 'address', 'Bilal Eroğlu Cd. No:3, Göreme / Nevşehir');
$hours = setting($pdo, $L === 'en' ? 'hours_en' : 'hours_tr', $L === 'en' ? '10:00 – 23:00' : '10.00 – 23.00');

$t = [
    'title' => $L === 'en' ? 'Welcome to Kebapzade' : 'Kebapzade’ye Hoş Geldiniz',
    'tagline' => $L === 'en'
        ? 'Traditional Anatolian recipes, stone oven magic and charcoal grills in the heart of Cappadocia.'
        : 'Kapadokya’nın kalbinde taş fırın ateşi, kömür ızgarası ve asırlık Anadolu lezzetleri.',
    'open' => $L === 'en' ? 'Open' : 'Açık',
    'view_menu' => $L === 'en' ? 'Explore Digital QR Menu' : 'Dijital QR Menüyü İncele',
    'menu_sub' => $L === 'en' ? 'Dishes, pottery kebab, appetizers & drinks' : 'Yemekler, testi kebabı, mezeler ve içecekler',
    'badge_testi' => $L === 'en' ? 'Famous Pottery Kebab' : 'Meşhur Testi Kebabı',
    'badge_fire' => $L === 'en' ? 'Stone Oven & Charcoal' : 'Taş Fırın & Kömür Ateşi',
    'badge_rating' => '4.9 ★ (1.200+ Reviews)',
    'est' => 'EST. 2008 · GÖREME · CAPPADOCIA',
];
?>
<!doctype html>
<html lang="<?=$L?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="theme-color" content="#160805">
<title><?=e($restaurant)?> | QR Menü & Karşılama</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?=e(base_url('/assets/css/qr-menu.css?v=' . filemtime(__DIR__ . '/assets/css/qr-menu.css')))?>">
<script>
  (function() {
    var theme = localStorage.getItem('kebapzade_theme') || 'light';
    document.documentElement.setAttribute('data-theme', theme);
  })();
</script>
</head>
<body class="welcome-page" style="--welcome-image:url('<?=e($welcomeImage)?>')">

<div class="welcome-ambient-glow" aria-hidden="true"></div>

<main class="welcome-shell">
  <!-- Top Navigation & Prominent Language Switcher -->
  <header class="welcome-topbar">
    <div class="welcome-status-pill">
      <span class="status-dot"></span>
      <span><?=e($t['open'])?> · <?=e($hours)?></span>
    </div>
    
    <div class="welcome-topbar-right">
      <!-- Dark / Light Theme Toggle Button -->
      <button type="button" class="theme-toggle-btn" data-theme-toggle title="<?= $L === 'en' ? 'Toggle Dark / Light Theme' : 'Aydınlık / Karanlık Mod' ?>" aria-label="<?= $L === 'en' ? 'Toggle Theme' : 'Görünüm Modunu Değiştir' ?>">
        <span class="theme-icon-sun" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
        </span>
        <span class="theme-icon-moon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
        </span>
      </button>

      <nav class="menu-languages prominent" aria-label="Language selection">
        <a class="<?=$L === 'tr' ? 'active' : ''?>" href="?lang=tr" title="Türkçe">TR</a>
        <a class="<?=$L === 'en' ? 'active' : ''?>" href="?lang=en" title="English">EN</a>
      </nav>
    </div>
  </header>

  <!-- Clean Minimal Central Card (Only Logo and Digital QR Menu) -->
  <section class="welcome-card clean-hero" aria-labelledby="welcome-title">
    <div class="welcome-brand">
      <img src="<?=e($logo)?>" alt="<?=e($restaurant)?>" class="welcome-logo" width="300" height="94">
    </div>

    <div class="welcome-pill-ribbon">
      <span class="ribbon-pill"><?=e($t['badge_testi'])?></span>
      <span class="ribbon-pill"><?=e($t['badge_fire'])?></span>
      <span class="ribbon-pill gold"><?=e($t['badge_rating'])?></span>
    </div>

    <h1 id="welcome-title"><?=e($t['title'])?></h1>
    <p class="welcome-copy"><?=e($t['tagline'])?></p>

    <!-- Main QR Menu Action Button -->
    <a class="welcome-primary-btn" href="<?=e(base_url('/qr-menu.php?lang='.$L))?>">
      <div class="btn-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3zM18 14v3M14 18h3M18 21v.01M14 14h.01M21 18v3"/></svg>
      </div>
      <div class="btn-text">
        <strong><?=e($t['view_menu'])?></strong>
        <small><?=e($t['menu_sub'])?></small>
      </div>
      <div class="btn-arrow" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </div>
    </a>
  </section>

  <!-- Footer Info -->
  <footer class="welcome-foot">
    <p class="foot-est"><?=e($t['est'])?></p>
    <p class="foot-address"><?=e($address)?></p>
  </footer>
</main>

<script src="<?=e(base_url('/assets/js/qr-menu.js?v=' . filemtime(__DIR__ . '/assets/js/qr-menu.js')))?>"></script>
</body>
</html>
