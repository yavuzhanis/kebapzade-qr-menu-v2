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
$fridayNotice = trim(setting($pdo, $L === 'en' ? 'friday_notice_en' : 'friday_notice_tr'));
if ($fridayNotice === '') $fridayNotice = $L === 'en' ? 'On Fridays, we open in the afternoon.' : 'Cuma günleri öğleden sonra açığız.';
$instagram = trim(setting($pdo, 'instagram', 'https://www.instagram.com/kebapzaderestaurant/'));
if ($instagram === '') $instagram = 'https://www.instagram.com/kebapzaderestaurant/';

// Hakkımızda verileri
$aboutSubtitle = setting($pdo, $L === 'en' ? 'about_subtitle_en' : 'about_subtitle_tr');
if ($aboutSubtitle === '') $aboutSubtitle = $L === 'en' ? 'Our Esteemed Guests' : 'Değerli misafirlerimiz';

$aboutTitle = setting($pdo, $L === 'en' ? 'about_title_en' : 'about_title_tr');
if ($aboutTitle === '') $aboutTitle = $L === 'en' ? 'From 2008 to the Present' : '2008 Yılından Bugüne Kadar';

$defaultTextTr = "Bir işletmeci olarak önceliğim sizin memnuniyetinizdir.\nBunu gerçekleştirmek için öncelikle yüce Allah (c.c)'ın yardımı ve kardeşlerimle gece ve gündüz samimi bir uğraş vermekteyiz. Kullandığımız tüm malzemeler 1. sınıf ve yöresinden olmasına azami derecede gayret ediyoruz, hazırladığımız tüm yemekleri aslına uygun (orijinal) ilk haliyle sizlere sunma gayretindeyiz.";
$defaultTextEn = "As a business owner, our highest priority is your satisfaction and genuine hospitality.\nTogether with my family and team, we strive night and day with passion. We make utmost efforts to source all our ingredients first-class and directly from their authentic native regions, preparing all our dishes true to their original traditional recipes.";

$aboutText = setting($pdo, $L === 'en' ? 'about_text_en' : 'about_text_tr');
if ($aboutText === '') {
    $aboutText = $L === 'en' ? $defaultTextEn : $defaultTextTr;
}

$aboutSign = setting($pdo, $L === 'en' ? 'about_sign_en' : 'about_sign_tr');
if ($aboutSign === '') $aboutSign = $L === 'en' ? 'Warm regards, Cappadocia Kebapzade Family.' : 'Saygılarımızla Kapadokya Kebapzade ailesi.';

$quoteTitle = setting($pdo, $L === 'en' ? 'about_quote_title_en' : 'about_quote_title_tr');
if ($quoteTitle === '') $quoteTitle = $L === 'en' ? '“Pottery kebab and Hatay casserole”' : '“Eşimle testi kebabı ve Hatay”';

$quoteText = setting($pdo, $L === 'en' ? 'about_quote_en' : 'about_quote_tr');
if ($quoteText === '') {
    $quoteText = $L === 'en'
        ? 'We had the pottery kebab and Hatay casserole with my wife, both were extraordinary! Portions and quality were top-notch. Absolutely the best dining spot in Göreme.'
        : 'Eşimle testi kebabı ve Hatay güveç yedik ikisi de mükemmeldi. Fiyatları da porsiyona göre uygundu. Göremede yemek yenecek en iyi yer diyebilirim zaten turistler de çok tercih ediyor.';
}

$quoteAuthor = setting($pdo, $L === 'en' ? 'about_quote_author_en' : 'about_quote_author_tr');
if ($quoteAuthor === '') $quoteAuthor = $L === 'en' ? 'Guest Review · Tripadvisor' : 'Müşteri Değerlendirmesi · Tripadvisor';

$image1 = image_url(setting($pdo, 'about_image_1', '/assets/images/about-1.jpg')) ?: base_url('/assets/images/about-1.jpg');
$image2 = image_url(setting($pdo, 'about_image_2', '/assets/images/about-2.jpg')) ?: base_url('/assets/images/about-2.jpg');

$t = [
    'title' => $L === 'en' ? 'Welcome to Kebapzade' : 'Kebapzade’ye Hoş Geldiniz',
    'tagline' => $L === 'en'
        ? 'Traditional Anatolian recipes, stone oven magic and charcoal grills in the heart of Cappadocia.'
        : 'Kapadokya’nın kalbinde taş fırın ateşi, kömür ızgarası ve asırlık Anadolu lezzetleri.',
    'open' => $L === 'en' ? 'Open' : 'Açık',
    'view_menu' => $L === 'en' ? 'Explore Digital QR Menu' : 'Dijital QR Menüyü İncele',
    'menu_sub' => $L === 'en' ? 'Dishes, pottery kebab, appetizers & drinks' : 'Yemekler, testi kebabı, mezeler ve içecekler',
    'about_btn' => $L === 'en' ? 'About Us & Our Story' : 'Hakkımızda & Hikâyemiz',
    'about_sub' => $L === 'en' ? 'Culinary journey since 2008' : '2008’den bugüne lezzet yolculuğumuz',
    'instagram' => $L === 'en' ? 'Visit us on Instagram' : 'Instagram’da Bizi Takip Edin',
    'instagram_sub' => '@kebapzaderestaurant',
    'badge_testi' => $L === 'en' ? 'Famous Pottery Kebab' : 'Meşhur Testi Kebabı',
    'badge_fire' => $L === 'en' ? 'Stone Oven & Charcoal' : 'Taş Fırın & Kömür Ateşi',
    'badge_rating' => '4.9 ★ (1.200+ Reviews)',
    'est' => 'EST. 2008 · GÖREME · CAPPADOCIA',
    'full_page_link' => $L === 'en' ? 'View Full Page ↗' : 'Detaylı Sayfayı Gör ↗',
    'tripadvisor' => 'Tripadvisor',
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

  <!-- Clean Minimal Central Card -->
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
    <div class="friday-notice" role="note">
      <span class="friday-notice-dot" aria-hidden="true"></span>
      <strong><?=e($fridayNotice)?></strong>
    </div>

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

    <!-- Hakkımızda Action Button (Opens sleek mini-modal sheet & links to hakkimizda.php) -->
    <button type="button" class="welcome-primary-btn about-btn" data-about-modal-trigger aria-haspopup="dialog" aria-expanded="false">
      <div class="btn-icon about-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
      </div>
      <div class="btn-text">
        <strong><?=e($t['about_btn'])?></strong>
        <small><?=e($t['about_sub'])?></small>
      </div>
      <div class="btn-arrow" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </div>
    </button>

    <!-- Instagram Action Button -->
    <a class="welcome-primary-btn instagram-btn" href="<?=e($instagram)?>" target="_blank" rel="noopener" aria-label="Instagram">
      <div class="btn-icon instagram-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"></rect><circle cx="12" cy="12" r="4"></circle><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"></circle></svg>
      </div>
      <div class="btn-text">
        <strong><?=e($t['instagram'])?></strong>
        <small><?=e($t['instagram_sub'])?></small>
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
    <div class="foot-about-wrap">
      <a class="qr-footer-about-pill" href="<?=e(base_url('/hakkimizda.php?lang='.$L))?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
        <span><?=e($t['about_btn'])?> ↗</span>
      </a>
    </div>
  </footer>
</main>

<!-- Hakkımızda Mini Sheet / Modal ("Küçük bir sayfa") -->
<div class="about-sheet-backdrop" id="aboutModal" role="dialog" aria-modal="true" aria-labelledby="aboutModalTitle" hidden>
  <div class="about-sheet-modal">
    <div class="about-sheet-drag-handle" aria-hidden="true"></div>
    <header class="about-sheet-header">
      <div class="about-sheet-brand">
        <img src="<?=e($logo)?>" alt="<?=e($restaurant)?>" class="about-sheet-logo" width="160" height="50">
        <span class="about-sheet-tagline"><?=e($t['est'])?></span>
      </div>
      <button type="button" class="about-sheet-close-btn" id="closeAboutModal" aria-label="Kapat">&times;</button>
    </header>

    <div class="about-sheet-body">
      <div class="about-sheet-hero-badge"><?=e($aboutSubtitle)?></div>
      <h2 id="aboutModalTitle" class="about-sheet-title"><?=e($aboutTitle)?></h2>

      <!-- Mini Photo Showcase -->
      <div class="about-sheet-gallery">
        <?php if ($image1): ?>
          <div class="sheet-photo-wrap">
            <img src="<?=e($image1)?>" alt="Taş Fırın & Testi Kebabı" loading="lazy">
            <span class="sheet-photo-label">Taş Fırın & Kömür Ateşi</span>
          </div>
        <?php endif; ?>
        <?php if ($image2): ?>
          <div class="sheet-photo-wrap">
            <img src="<?=e($image2)?>" alt="Geleneksel Testi Kebabı" loading="lazy">
            <span class="sheet-photo-label">Geleneksel Testi Kebabı</span>
          </div>
        <?php endif; ?>
      </div>

      <!-- Story Text -->
      <div class="about-sheet-text">
        <?php 
        $paragraphs = preg_split('/\r\n\r\n|\n\n|\r\r/', $aboutText);
        foreach ($paragraphs as $para):
          $para = trim($para);
          if ($para === '') continue;
        ?>
          <p><?=nl2br(e($para))?></p>
        <?php endforeach; ?>
      </div>

      <div class="about-sheet-sign">
        <strong><?=e($aboutSign)?></strong>
      </div>

      <!-- Tripadvisor Review Card in Modal -->
      <div class="about-sheet-review">
        <div class="sheet-review-top">
          <span class="sheet-review-ta"><?=e($t['tripadvisor'])?></span>
          <span class="sheet-review-stars">★★★★★</span>
        </div>
        <?php if ($quoteTitle): ?>
          <strong style="display:block; font-size:13px; margin-bottom:4px; color:var(--ink-primary);"><?=e($quoteTitle)?></strong>
        <?php endif; ?>
        <blockquote class="sheet-review-quote">“<?=e($quoteText)?>”</blockquote>
        <span class="sheet-review-author"><?=e($quoteAuthor)?></span>
      </div>

      <!-- Modal Action Footer -->
      <div class="about-sheet-actions">
        <a href="<?=e(base_url('/hakkimizda.php?lang='.$L))?>" class="sheet-action-link">
          <span><?=e($t['full_page_link'])?></span>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14L21 3"/></svg>
        </a>

        <a href="<?=e(base_url('/qr-menu.php?lang='.$L))?>" class="sheet-action-btn-primary">
          <span><?=e($t['view_menu'])?></span>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
      </div>
    </div>
  </div>
</div>

<script src="<?=e(base_url('/assets/js/qr-menu.js?v=' . filemtime(__DIR__ . '/assets/js/qr-menu.js')))?>"></script>
</body>
</html>
