<?php
declare(strict_types=1);
require __DIR__ . '/app/bootstrap.php';

$L = lang();
$nameCol = $L === 'en' ? 'name_en' : 'name_tr';
$descCol = $L === 'en' ? 'description_en' : 'description_tr';
$labelCol = $L === 'en' ? 'label_en' : 'label_tr';
$noteCol = $L === 'en' ? 'price_note_en' : 'price_note_tr';

$restaurant = setting($pdo, 'restaurant_name', 'Kapadokya Kebapzade Restaurant');
$logo = image_url(setting($pdo, 'logo_image', '/assets/images/logo.svg'));
if (!$logo) {
    $logo = base_url('/assets/images/logo.svg');
}
$phone = setting($pdo, 'phone', '+90 384 271 30 12');
$whatsapp = preg_replace('/\D+/', '', setting($pdo, 'whatsapp', '903842713012'));
$maps = setting($pdo, 'maps_url', 'https://www.google.com/maps/search/?api=1&query=Kebapzade+Goreme');
$wifiPass = setting($pdo, 'wifi_pass', 'kebapzade2026');
$hours = setting($pdo, $L === 'en' ? 'hours_en' : 'hours_tr', $L === 'en' ? '10:00 – 23:00' : '10.00 – 23.00');

$tableParam = trim((string)($_GET['table'] ?? ''));

$cats = $pdo->query('SELECT * FROM categories WHERE is_active=1 ORDER BY sort_order,id')->fetchAll();
$itemQ = $pdo->prepare('SELECT * FROM items WHERE category_id=? AND is_active=1 ORDER BY sort_order,id');
$varQ = $pdo->prepare('SELECT * FROM item_variants WHERE item_id=? ORDER BY sort_order,id');

function qr_money(?string $v): string {
    if ($v === null || $v === '' || !is_numeric($v)) return '';
    $n = (float)$v;
    return '₺' . number_format($n, floor($n) == $n ? 0 : 2, ',', '.');
}


$t = [
    'menu' => $L === 'en' ? 'Digital Menu' : 'Dijital Menü',
    'search' => $L === 'en' ? 'Search dishes, drinks or ingredients...' : 'Yemek, içecek veya içerik ara...',
    'none' => $L === 'en' ? 'No dishes match your search.' : 'Aramanıza uygun ürün bulunamadı.',
    'items' => $L === 'en' ? 'dishes' : 'ürün',
    'featured' => $L === 'en' ? 'Chef’s Signature' : 'Şefin İmzası',
    'veg' => $L === 'en' ? 'Vegetarian' : 'Vejetaryen',
    'spicy' => $L === 'en' ? 'Spicy' : 'Acılı',
    'allergen' => $L === 'en' ? 'Please inform our service team regarding food allergies and dietary preferences.' : 'Alerjiniz veya özel beslenme tercihiniz varsa lütfen servis ekibimizi bilgilendirin.',
    'filter_all' => $L === 'en' ? 'All' : 'Tümü',
    'filter_featured' => $L === 'en' ? 'Signature' : 'İmza Lezzetler',
    'filter_spicy' => $L === 'en' ? 'Spicy' : 'Acılı',
    'filter_veg' => $L === 'en' ? 'Vegetarian' : 'Vejetaryen',
    'table_prefix' => $L === 'en' ? 'Table' : 'Masa',
    'wifi_copied' => $L === 'en' ? 'Wi-Fi password copied!' : 'Wi-Fi şifresi kopyalandı!',
    'tap_to_zoom' => $L === 'en' ? 'Tap photo to zoom' : 'Fotoğrafı büyütmek için dokunun',
];
?>
<!doctype html>
<html lang="<?=$L?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="theme-color" content="#1a0805">
<title><?=e($restaurant)?> | <?=e($t['menu'])?></title>
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
<body class="menu-page">

<!-- Clean Sticky Dock: Search Input, Theme Toggle & Language Switch -->
<div class="menu-sticky-dock">
  <div class="search-wrap-unified">
    <div class="search-input-shell">
      <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="11" cy="11" r="8"></circle>
        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
      </svg>
      <input type="search" data-menu-search placeholder="<?=e($t['search'])?>" autocomplete="off" spellcheck="false">
      <button type="button" class="search-clear-btn" data-search-clear aria-label="Clear" hidden>&times;</button>
    </div>

    <div class="search-meta-actions">
      <?php if ($tableParam): ?>
        <div class="table-badge">
          <span><?=e($t['table_prefix'])?> <b><?=e($tableParam)?></b></span>
        </div>
      <?php endif; ?>

      <button type="button" class="theme-toggle-btn" data-theme-toggle title="<?= $L === 'en' ? 'Toggle Dark / Light Theme' : 'Aydınlık / Karanlık Mod' ?>" aria-label="<?= $L === 'en' ? 'Toggle Theme' : 'Görünüm Modunu Değiştir' ?>">
        <span class="theme-icon-sun" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
        </span>
        <span class="theme-icon-moon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
        </span>
      </button>

      <nav class="menu-languages" aria-label="Language Switch">
        <a class="<?=$L === 'tr' ? 'active' : ''?>" href="?lang=tr<?= $tableParam ? '&table='.urlencode($tableParam) : '' ?>">TR</a>
        <a class="<?=$L === 'en' ? 'active' : ''?>" href="?lang=en<?= $tableParam ? '&table='.urlencode($tableParam) : '' ?>">EN</a>
      </nav>
    </div>
  </div>
</div>

<!-- Main Menu Content -->
<main class="menu-content-wrap">
  <div class="search-results-feedback" data-results-counter hidden></div>
  <div class="no-results-card" data-no-results hidden>
    <div class="no-results-icon">
      <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
    </div>
    <h3><?=e($t['none'])?></h3>
    <button type="button" class="btn-reset-search" data-reset-search><?=e($t['filter_all'])?></button>
  </div>

  <div class="category-sections-list">
    <?php foreach ($cats as $cat): 
      $itemQ->execute([$cat['id']]);
      $items = $itemQ->fetchAll();
      if (!$items) continue;
      
      $catName = $cat[$nameCol] ?: $cat['name_tr'];
      $catDesc = $cat[$descCol] ?: $cat['description_tr'];
      $cover = image_url($cat['image_path'] ?? '');
      if (!$cover) {
        $cover = base_url('/assets/images/hero.jpg');
      }
      $catSearch = $cat['name_tr'].' '.$cat['name_en'].' '.$cat['description_tr'].' '.$cat['description_en'];
    ?>
    <section class="menu-category-section" id="cat-<?=e($cat['slug'])?>" data-category data-category-search="<?=e($catSearch)?>">
      <!-- Category Banner Card (Tap to Expand/Collapse Dishes) -->
      <button type="button" class="category-hero-card" data-category-toggle aria-expanded="false" style="--cat-bg:url('<?=e($cover)?>')">
        <div class="cat-overlay"></div>
        <div class="cat-hero-info">
          <div class="cat-badge-line">
            <span class="cat-count-badge"><?=count($items)?> <?=e($t['items'])?></span>
          </div>
          <h2 class="cat-hero-title"><?=e($catName)?></h2>
          <?php if ($catDesc): ?>
            <p class="cat-hero-desc"><?=e($catDesc)?></p>
          <?php endif; ?>
        </div>
        <div class="cat-toggle-indicator" aria-hidden="true">
          <svg class="chevron-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
        </div>
      </button>

      <!-- Dishes Container (Hidden by default, opens on click) -->
      <div class="category-dishes-panel" data-dishes-panel hidden>
        <div class="dishes-grid">
          <?php foreach ($items as $item): 
            $varQ->execute([$item['id']]);
            $variants = $varQ->fetchAll();
            $iname = $item[$nameCol] ?: $item['name_tr'];
            $idesc = $item[$descCol] ?: $item['description_tr'];
            $img = image_url($item['image_path']);
            if (!$img) {
              $img = $cover;
            }
            $itemPrice = $item['price'] !== null ? (float)$item['price'] : 0.0;
            $formattedPrice = qr_money((string)$item['price']);
            $itemSearch = $item['name_tr'].' '.$item['name_en'].' '.$item['description_tr'].' '.$item['description_en'].' '.$catSearch;
          ?>
          <article class="dish-card" 
                   data-menu-item 
                   data-id="<?=e((string)$item['id'])?>"
                   data-name="<?=e($iname)?>"
                   data-price="<?=e((string)$itemPrice)?>"
                   data-formatted-price="<?=e($formattedPrice)?>"
                   data-img="<?=e($img)?>"
                   data-desc="<?=e($idesc)?>"
                   data-featured="<?=$item['is_featured'] ? '1' : '0'?>"
                   data-spicy="<?=$item['is_spicy'] ? '1' : '0'?>"
                   data-veg="<?=$item['is_vegetarian'] ? '1' : '0'?>"
                   data-search="<?=e($itemSearch)?>">
            
            <!-- Food Photo (Tap to zoom image) -->
            <div class="dish-media-wrap" 
                 data-lightbox-trigger 
                 data-lightbox-img="<?=e($img)?>" 
                 data-lightbox-title="<?=e($iname)?>" 
                 data-lightbox-price="<?=e($formattedPrice)?>"
                 title="<?=e($t['tap_to_zoom'])?>">
              <img src="<?=e($img)?>" alt="<?=e($iname)?>" loading="lazy" class="dish-img">
              <div class="dish-floating-badges">
                <?php if ($item['is_featured']): ?>
                  <span class="badge-featured" title="<?=e($t['featured'])?>"><?=e($t['featured'])?></span>
                <?php endif; ?>
                <?php if ($item['is_vegetarian']): ?>
                  <span class="badge-veg" title="<?=e($t['veg'])?>"><?=e($t['veg'])?></span>
                <?php endif; ?>
                <?php if ($item['is_spicy']): ?>
                  <span class="badge-spicy" title="<?=e($t['spicy'])?>"><?=e($t['spicy'])?></span>
                <?php endif; ?>
              </div>
              <div class="dish-zoom-indicator">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="11" y1="8" x2="11" y2="14"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
              </div>
            </div>

            <!-- Dish Content: Title, Price, Description, Variants -->
            <div class="dish-body">
              <div class="dish-head">
                <h3 class="dish-title"><?=e($iname)?></h3>
                <?php if ($formattedPrice): ?>
                  <span class="dish-price"><?=e($formattedPrice)?></span>
                <?php endif; ?>
              </div>

              <?php if ($idesc): ?>
                <p class="dish-description"><?=e($idesc)?></p>
              <?php endif; ?>

              <?php if (!empty($item[$noteCol])): ?>
                <div class="dish-note"><?=e($item[$noteCol])?></div>
              <?php endif; ?>

              <?php if ($variants): ?>
                <div class="dish-variants-pill-list">
                  <?php foreach ($variants as $v): ?>
                    <span class="var-pill"><?=e($v[$labelCol] ?: $v['label_tr'])?>: <b><?=e(qr_money($v['price']))?></b></span>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php endforeach; ?>
  </div>

  <!-- Allergen and Quality Banner -->
  <aside class="allergen-box">
    <div class="allergen-icon">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
    </div>
    <div class="allergen-content">
      <strong>Kapadokya Kebapzade Gurme Standartları</strong>
      <p><?=e($t['allergen'])?></p>
    </div>
  </aside>

  <!-- Footer -->
  <footer class="qr-luxury-footer">
    <div class="foot-crest">
      <img src="<?=e($logo)?>" alt="<?=e($restaurant)?>" width="200" height="62">
    </div>
    <p class="foot-heritage">Göreme · Cappadocia · Est. 2008</p>
    <p class="foot-quote">"Lezzetli ve kaliteli yemek tesadüf değildir."</p>
  </footer>
</main>

<!-- Floating Circular WhatsApp Button (As in User Screenshot) -->
<?php if ($whatsapp): ?>
<a href="https://wa.me/<?=e($whatsapp)?><?= $tableParam ? '?text='.urlencode('Merhaba Kebapzade, Masa '.$tableParam.' için servis rica ediyoruz.') : '' ?>" 
   target="_blank" 
   rel="noopener" 
   class="floating-wa-circle" 
   aria-label="WhatsApp İletişim">
  <svg viewBox="0 0 24 24" fill="currentColor">
    <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2zm5.8 14.15c-.24.67-1.39 1.28-1.92 1.34-.51.06-1.16.08-3.76-.99-2.22-.92-3.66-3.18-3.77-3.33-.11-.15-.9-1.2-0.9-2.29s.57-1.63.78-1.85c.2-.22.45-.28.6-.28.15 0 .3 0 .43.01.14.01.32-.05.5.39.19.46.65 1.58.71 1.7.06.12.09.26.02.41-.08.15-.12.24-.24.38-.12.14-.26.31-.37.42-.12.12-.25.26-.11.5.14.24.63 1.04 1.35 1.68.93.83 1.71 1.09 1.95 1.21.24.12.38.1.52-.06.15-.17.61-.71.77-.96.17-.24.33-.2.56-.12.23.08 1.48.7 1.73.82.25.13.42.19.48.3.06.11.06.66-.18 1.33z"/>
  </svg>
</a>
<?php endif; ?>

<!-- Image Lightbox Modal (Tap photo to zoom) -->
<div class="image-lightbox-backdrop" id="imageLightbox" hidden>
  <div class="image-lightbox-card">
    <button type="button" class="lightbox-close-btn" id="closeLightbox" aria-label="Kapat">&times;</button>
    <div class="lightbox-img-wrap">
      <img src="" alt="" id="lightboxImg" class="lightbox-img">
    </div>
    <div class="lightbox-caption">
      <h3 id="lightboxTitle"></h3>
      <span id="lightboxPrice" class="lightbox-price"></span>
    </div>
  </div>
</div>

<!-- Back to top button -->
<button class="back-to-top-btn" type="button" data-back-top aria-label="Yukarı çık">↑</button>

<!-- Toast notification -->
<div class="toast-notification" id="toast" role="status" aria-live="polite"></div>

<script src="<?=e(base_url('/assets/js/qr-menu.js?v=' . filemtime(__DIR__ . '/assets/js/qr-menu.js')))?>"></script>
</body>
</html>
