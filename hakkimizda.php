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

$phone = setting($pdo, 'phone', '+90 384 271 30 12');
$whatsapp = preg_replace('/\D+/', '', setting($pdo, 'whatsapp', '903842713012'));
$maps = setting($pdo, 'maps_url', 'https://www.google.com/maps/search/?api=1&query=Kebapzade+Goreme');
$address = setting($pdo, 'address', 'Bilal Eroğlu Cd. No:3, Göreme / Nevşehir');
$hours = setting($pdo, $L === 'en' ? 'hours_en' : 'hours_tr', $L === 'en' ? '10:00 – 23:00' : '10.00 – 23.00');
$fridayNotice = trim(setting($pdo, $L === 'en' ? 'friday_notice_en' : 'friday_notice_tr'));
if ($fridayNotice === '') {
    $fridayNotice = $L === 'en' ? 'On Fridays, we open in the afternoon.' : 'Cuma günleri öğleden sonra açığız.';
}
$instagram = trim(setting($pdo, 'instagram', 'https://www.instagram.com/kebapzaderestaurant/'));
if ($instagram === '') {
    $instagram = 'https://www.instagram.com/kebapzaderestaurant/';
}

// Hakkımızda içerikleri
$aboutSubtitle = setting($pdo, $L === 'en' ? 'about_subtitle_en' : 'about_subtitle_tr');
if ($aboutSubtitle === '') {
    $aboutSubtitle = $L === 'en' ? 'Our Esteemed Guests' : 'Değerli misafirlerimiz';
}

$aboutTitle = setting($pdo, $L === 'en' ? 'about_title_en' : 'about_title_tr');
if ($aboutTitle === '') {
    $aboutTitle = $L === 'en' ? 'From 2008 to the Present' : '2008 Yılından Bugüne Kadar';
}

$defaultTextTr = "Bir işletmeci olarak önceliğim sizin memnuniyetinizdir.\nBunu gerçekleştirmek için öncelikle yüce Allah (c.c)'ın yardımı ve kardeşlerimle gece ve gündüz samimi bir uğraş vermekteyiz. Kullandığımız tüm malzemeler 1. sınıf ve yöresinden olmasına azami derecede gayret ediyoruz, hazırladığımız tüm yemekleri aslına uygun (orijinal) ilk haliyle sizlere sunma gayretindeyiz.";
$defaultTextEn = "As a business owner, our highest priority is your satisfaction and genuine hospitality.\nTogether with my family and team, we strive night and day with passion. We make utmost efforts to source all our ingredients first-class and directly from their authentic native regions, preparing all our dishes true to their original traditional recipes.";

$aboutText = setting($pdo, $L === 'en' ? 'about_text_en' : 'about_text_tr');
if ($aboutText === '') {
    $aboutText = $L === 'en' ? $defaultTextEn : $defaultTextTr;
}

$aboutSign = setting($pdo, $L === 'en' ? 'about_sign_en' : 'about_sign_tr');
if ($aboutSign === '') {
    $aboutSign = $L === 'en' ? 'Warm regards, Cappadocia Kebapzade Family.' : 'Saygılarımızla Kapadokya Kebapzade ailesi.';
}

$quoteTitle = setting($pdo, $L === 'en' ? 'about_quote_title_en' : 'about_quote_title_tr');
if ($quoteTitle === '') {
    $quoteTitle = $L === 'en' ? '“Pottery kebab and Hatay casserole”' : '“Eşimle testi kebabı ve Hatay”';
}

$quoteText = setting($pdo, $L === 'en' ? 'about_quote_en' : 'about_quote_tr');
if ($quoteText === '') {
    $quoteText = $L === 'en'
        ? 'We had the pottery kebab and Hatay casserole with my wife, both were extraordinary! Portions and quality were top-notch. Absolutely the best dining spot in Göreme.'
        : 'Eşimle testi kebabı ve Hatay güveç yedik ikisi de mükemmeldi. Fiyatları da porsiyona göre uygundu. Göremede yemek yenecek en iyi yer diyebilirim zaten turistler de çok tercih ediyor.';
}

$quoteAuthor = setting($pdo, $L === 'en' ? 'about_quote_author_en' : 'about_quote_author_tr');
if ($quoteAuthor === '') {
    $quoteAuthor = $L === 'en' ? 'Guest Review · Tripadvisor' : 'Müşteri Değerlendirmesi · Tripadvisor';
}

$ratingScore = setting($pdo, 'about_rating', '5.0');

$image1 = image_url(setting($pdo, 'about_image_1', '/assets/images/about-1.jpg')) ?: base_url('/assets/images/about-1.jpg');
$image2 = image_url(setting($pdo, 'about_image_2', '/assets/images/about-2.jpg')) ?: base_url('/assets/images/about-2.jpg');

$t = [
    'page_title' => $L === 'en' ? 'About Us & Our Heritage' : 'Hakkımızda & Hikâyemiz',
    'back_home' => $L === 'en' ? '← Home' : '← Karşılama',
    'back_menu' => $L === 'en' ? 'Digital QR Menu' : 'Dijital QR Menü',
    'est' => 'EST. 2008 · GÖREME · CAPPADOCIA',
    'values_heading' => $L === 'en' ? 'Our Gourmet Standards' : 'Kebapzade Gurme Standartları',
    'val1_title' => $L === 'en' ? '1st Class Regional Ingredients' : '1. Sınıf & Yöresinden Malzemeler',
    'val1_desc' => $L === 'en' ? 'Hatay pomegranate molasses, Gaziantep pistachios, fresh regional meats and authentic spices.' : 'Hatay nar ekşisi, Antep fıstığı, seçkin taze etler ve yerinden temin edilen doğal baharatlar.',
    'val2_title' => $L === 'en' ? 'Stone Oven & Charcoal Fire' : 'Taş Fırın & Kömür Ateşi',
    'val2_desc' => $L === 'en' ? 'Famous pottery kebab, traditional casseroles and charcoal-grilled Anatolian recipes.' : 'Meşhur testi kebabı, taş fırın kiremit lezzetleri ve kömür ızgarasıyla aslına sadık pişirme.',
    'val3_title' => $L === 'en' ? 'Hospitality Since 2008' : '2008’den Beri Misafirperverlik',
    'val3_desc' => $L === 'en' ? 'Warm family hospitality in the scenic center of Göreme with unwavering commitment to quality.' : 'Kapadokya Göreme’nin kalbinde, aile sıcaklığı ve misafir memnuniyetini daima ilk sıraya koyan anlayış.',
    'tripadvisor' => 'Tripadvisor',
    'reviews_title' => $L === 'en' ? 'Guest Experiences' : 'Misafir Yorumları',
    'cta_title' => $L === 'en' ? 'Ready to Taste the Experience?' : 'Lezzetlerimizi Keşfetmeye Hazır Mısınız?',
    'cta_desc' => $L === 'en' ? 'Explore our digital QR menu with pottery kebabs, regional appetizers and desserts.' : 'Kapadokya’nın tescilli lezzetleri, taş fırın spesiyalleri ve taze mezelerimiz sizleri bekliyor.',
    'explore_menu' => $L === 'en' ? 'Explore Digital QR Menu →' : 'Dijital QR Menüyü İncele →',
    'call' => $L === 'en' ? 'Call' : 'Telefon',
    'directions' => $L === 'en' ? 'Directions' : 'Yol Tarifi',
    'whatsapp' => 'WhatsApp',
    'hours_label' => $L === 'en' ? 'Hours' : 'Çalışma Saatleri',
    'address_label' => $L === 'en' ? 'Address' : 'Adres',
];
?>
<!doctype html>
<html lang="<?=$L?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="theme-color" content="#160805">
<title><?=e($restaurant)?> | <?=e($t['page_title'])?></title>
<meta name="description" content="<?=e($aboutSubtitle)?> - <?=e($aboutTitle)?>: <?=e(mb_substr(strip_tags($aboutText), 0, 160))?>">
<link rel="canonical" href="<?=e(absolute_url('/hakkimizda.php'))?>">
<meta property="og:title" content="<?=e($restaurant)?> | <?=e($t['page_title'])?>">
<meta property="og:description" content="<?=e($aboutTitle)?> - Kapadokya Kebapzade Restaurant Göreme">
<meta property="og:image" content="<?=e($image1)?>">
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
<body class="about-page-body">

<!-- Sticky Top Navigation -->
<nav class="about-nav-dock">
  <div class="about-nav-shell">
    <div class="about-nav-left">
      <a href="<?=e(base_url('/?lang='.$L))?>" class="about-back-btn" title="<?=e($t['back_home'])?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        <span><?=e($t['back_home'])?></span>
      </a>
      <a href="<?=e(base_url('/qr-menu.php?lang='.$L))?>" class="about-menu-pill">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3zM18 14v3M14 18h3M18 21v.01M14 14h.01M21 18v3"/></svg>
        <span><?=e($t['back_menu'])?></span>
      </a>
    </div>

    <div class="about-nav-right">
      <button type="button" class="theme-toggle-btn" data-theme-toggle title="<?= $L === 'en' ? 'Toggle Dark / Light Theme' : 'Aydınlık / Karanlık Mod' ?>" aria-label="<?= $L === 'en' ? 'Toggle Theme' : 'Görünüm Modunu Değiştir' ?>">
        <span class="theme-icon-sun" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
        </span>
        <span class="theme-icon-moon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
        </span>
      </button>

      <nav class="menu-languages prominent" aria-label="Language switch">
        <a class="<?=$L === 'tr' ? 'active' : ''?>" href="?lang=tr">TR</a>
        <a class="<?=$L === 'en' ? 'active' : ''?>" href="?lang=en">EN</a>
      </nav>
    </div>
  </div>
</nav>

<!-- Main Container -->
<main class="about-container">
  
  <!-- Hero Section -->
  <header class="about-hero-header">
    <div class="about-brand-crest">
      <a href="<?=e(base_url('/?lang='.$L))?>">
        <img src="<?=e($logo)?>" alt="<?=e($restaurant)?>" width="240" height="75" class="about-brand-logo">
      </a>
    </div>
    <div class="about-heritage-pill"><?=e($t['est'])?></div>
    <h1 class="about-page-main-title"><?=e($t['page_title'])?></h1>
    <div class="friday-notice about-hero-notice" role="note">
      <span class="friday-notice-dot" aria-hidden="true"></span>
      <strong><?=e($fridayNotice)?></strong>
    </div>
  </header>

  <!-- Story Card: Dual Column on Desktop, Mobile Optimized -->
  <section class="about-card-unified">
    <div class="about-story-grid">
      
      <!-- Text / Message Block -->
      <div class="about-text-column">
        <div class="about-subtitle-badge"><?=e($aboutSubtitle)?></div>
        <h2 class="about-story-title"><?=e($aboutTitle)?></h2>
        
        <div class="about-story-paragraphs">
          <?php 
          $paragraphs = preg_split('/\r\n\r\n|\n\n|\r\r/', $aboutText);
          foreach ($paragraphs as $para):
            $para = trim($para);
            if ($para === '') continue;
          ?>
            <p><?=nl2br(e($para))?></p>
          <?php endforeach; ?>
        </div>

        <div class="about-signature-wrap">
          <div class="signature-line" aria-hidden="true"></div>
          <strong class="signature-text"><?=e($aboutSign)?></strong>
        </div>
      </div>

      <!-- Visual Gallery Block -->
      <div class="about-visuals-column">
        <?php if ($image1): ?>
        <div class="about-photo-card main-photo">
          <img src="<?=e($image1)?>" alt="Kapadokya Kebapzade Taş Fırın" loading="lazy">
          <div class="about-photo-badge">Taş Fırın & Kömür Ateşi</div>
        </div>
        <?php endif; ?>

        <?php if ($image2): ?>
        <div class="about-photo-card secondary-photo">
          <img src="<?=e($image2)?>" alt="Kapadokya Kebapzade Sunumları" loading="lazy">
          <div class="about-photo-badge">Geleneksel Testi Kebabı</div>
        </div>
        <?php endif; ?>
      </div>

    </div>
  </section>

  <!-- Value Pillars -->
  <section class="about-values-section" aria-labelledby="standards-heading">
    <div class="about-section-head">
      <div class="gold-eyebrow">Kebapzade Göreme</div>
      <h3 id="standards-heading" class="about-section-title"><?=e($t['values_heading'])?></h3>
    </div>

    <div class="about-values-grid">
      <div class="value-card">
        <div class="value-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        </div>
        <h4><?=e($t['val1_title'])?></h4>
        <p><?=e($t['val1_desc'])?></p>
      </div>

      <div class="value-card">
        <div class="value-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg>
        </div>
        <h4><?=e($t['val2_title'])?></h4>
        <p><?=e($t['val2_desc'])?></p>
      </div>

      <div class="value-card">
        <div class="value-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
        </div>
        <h4><?=e($t['val3_title'])?></h4>
        <p><?=e($t['val3_desc'])?></p>
      </div>
    </div>
  </section>

  <!-- Tripadvisor Testimonial Block -->
  <section class="about-testimonial-section" aria-label="Tripadvisor Yorumu">
    <div class="testimonial-card">
      <div class="testimonial-badge-row">
        <div class="tripadvisor-pill">
          <span class="ta-dot"></span>
          <span><?=e($t['tripadvisor'])?></span>
        </div>
        <div class="testimonial-stars" aria-label="<?=e($ratingScore)?> Yıldız">
          ★★★★★
        </div>
      </div>

      <?php if ($quoteTitle): ?>
        <h4 class="testimonial-quote-title"><?=e($quoteTitle)?></h4>
      <?php endif; ?>

      <blockquote class="testimonial-quote">
        “<?=e($quoteText)?>”
      </blockquote>

      <div class="testimonial-author">
        <strong><?=e($quoteAuthor)?></strong>
        <span>Kapadokya Ziyareti</span>
      </div>
    </div>
  </section>

  <!-- Contact & Quick Info Strip -->
  <section class="about-quick-strip">
    <div class="quick-strip-card">
      <div class="quick-strip-item">
        <small><?=e($t['address_label'])?></small>
        <strong><?=e($address)?></strong>
        <?php if ($maps): ?>
          <a href="<?=e($maps)?>" target="_blank" rel="noopener" class="quick-link"><?=e($t['directions'])?> ↗</a>
        <?php endif; ?>
      </div>

      <div class="quick-strip-item">
        <small><?=e($t['hours_label'])?></small>
        <strong><?=e($hours)?></strong>
        <span><?=e($fridayNotice)?></span>
      </div>

      <div class="quick-strip-item actions">
        <?php if ($phone): ?>
          <a href="tel:<?=e(preg_replace('/[^\d+]/', '', $phone))?>" class="quick-btn-action">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            <span><?=e($phone)?></span>
          </a>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- Menu Call to Action Banner -->
  <section class="about-cta-banner">
    <h3><?=e($t['cta_title'])?></h3>
    <p><?=e($t['cta_desc'])?></p>
    <a href="<?=e(base_url('/qr-menu.php?lang='.$L))?>" class="welcome-primary-btn about-cta-btn">
      <div class="btn-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3zM18 14v3M14 18h3M18 21v.01M14 14h.01M21 18v3"/></svg>
      </div>
      <div class="btn-text">
        <strong><?=e($t['explore_menu'])?></strong>
        <small>Kebapzade Cappadocia</small>
      </div>
      <div class="btn-arrow" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </div>
    </a>
  </section>

  <!-- Footer -->
  <footer class="qr-luxury-footer">
    <div class="foot-crest">
      <img src="<?=e($logo)?>" alt="<?=e($restaurant)?>" width="180" height="56">
    </div>
    <p class="foot-heritage">Göreme · Cappadocia · Est. 2008</p>
    <p class="foot-quote">"Lezzetli ve kaliteli yemek tesadüf değildir."</p>
    <a class="qr-instagram-link" href="<?=e($instagram)?>" target="_blank" rel="noopener">Instagram · @kebapzaderestaurant</a>
  </footer>

</main>

<!-- Floating WhatsApp Action -->
<?php if ($whatsapp): ?>
<a href="https://wa.me/<?=e($whatsapp)?>?text=<?=urlencode('Merhaba Kebapzade, hakkınızda bilgi ve rezervasyon için yazıyorum.')?>" 
   target="_blank" 
   rel="noopener" 
   class="floating-wa-circle" 
   aria-label="WhatsApp İletişim">
  <svg viewBox="0 0 24 24" fill="currentColor">
    <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2zm5.8 14.15c-.24.67-1.39 1.28-1.92 1.34-.51.06-1.16.08-3.76-.99-2.22-.92-3.66-3.18-3.77-3.33-.11-.15-.9-1.2-0.9-2.29s.57-1.63.78-1.85c.2-.22.45-.28.6-.28.15 0 .3 0 .43.01.14.01.32-.05.5.39.19.46.65 1.58.71 1.7.06.12.09.26.02.41-.08.15-.12.24-.24.38-.12.14-.26.31-.37.42-.12.12-.25.26-.11.5.14.24.63 1.04 1.35 1.68.93.83 1.71 1.09 1.95 1.21.24.12.38.1.52-.06.15-.17.61-.71.77-.96.17-.24.33-.2.56-.12.23.08 1.48.7 1.73.82.25.13.42.19.48.3.06.11.06.66-.18 1.33z"/>
  </svg>
</a>
<?php endif; ?>

<!-- Toast Notification -->
<div class="toast-notification" id="toast" role="status" aria-live="polite"></div>

<script src="<?=e(base_url('/assets/js/qr-menu.js?v=' . filemtime(__DIR__ . '/assets/js/qr-menu.js')))?>"></script>
</body>
</html>
