<?php
declare(strict_types=1);
require __DIR__ . '/../app/bootstrap.php';
require_admin();
require_once __DIR__ . '/../app/upload.php';

$fields = [
    'about_subtitle_tr' => 'Üst Başlık (TR)',
    'about_subtitle_en' => 'Üst Başlık (EN)',
    'about_title_tr' => 'Ana Başlık (TR)',
    'about_title_en' => 'Ana Başlık (EN)',
    'about_text_tr' => 'Hikâye & Açıklama Metni (TR)',
    'about_text_en' => 'Hikâye & Açıklama Metni (EN)',
    'about_sign_tr' => 'İmza / Kapanış (TR)',
    'about_sign_en' => 'İmza / Kapanış (EN)',
    'about_quote_title_tr' => 'Tripadvisor Yorum Başlığı (TR)',
    'about_quote_title_en' => 'Tripadvisor Yorum Başlığı (EN)',
    'about_quote_tr' => 'Tripadvisor Yorum Metni (TR)',
    'about_quote_en' => 'Tripadvisor Yorum Metni (EN)',
    'about_quote_author_tr' => 'Değerlendiren / Unvan (TR)',
    'about_quote_author_en' => 'Değerlendiren / Unvan (EN)',
    'about_rating' => 'Değerlendirme Puanı (Örn: 5.0)',
];

$defaults = [
    'about_subtitle_tr' => 'Değerli misafirlerimiz',
    'about_subtitle_en' => 'Our Esteemed Guests',
    'about_title_tr' => '2008 Yılından Bugüne Kadar',
    'about_title_en' => 'From 2008 to the Present',
    'about_text_tr' => "Bir işletmeci olarak önceliğim sizin memnuniyetinizdir.\nBunu gerçekleştirmek için öncelikle yüce Allah (c.c)'ın yardımı ve kardeşlerimle gece ve gündüz samimi bir uğraş vermekteyiz. Kullandığımız tüm malzemeler 1. sınıf ve yöresinden olmasına azami derecede gayret ediyoruz, hazırladığımız tüm yemekleri aslına uygun (orijinal) ilk haliyle sizlere sunma gayretindeyiz.",
    'about_text_en' => "As a business owner, our highest priority is your satisfaction and genuine hospitality.\nTogether with my family and team, we strive night and day with passion. We make utmost efforts to source all our ingredients first-class and directly from their authentic native regions, preparing all our dishes true to their original traditional recipes.",
    'about_sign_tr' => 'Saygılarımızla Kapadokya Kebapzade ailesi.',
    'about_sign_en' => 'Warm regards, Cappadocia Kebapzade Family.',
    'about_quote_title_tr' => '“Eşimle testi kebabı ve Hatay”',
    'about_quote_title_en' => '“Pottery kebab and Hatay casserole”',
    'about_quote_tr' => 'Eşimle testi kebabı ve Hatay güveç yedik ikisi de mükemmeldi. Fiyatları da porsiyona göre uygundu. Göremede yemek yenecek en iyi yer diyebilirim zaten turistler de çok tercih ediyor.',
    'about_quote_en' => 'We had the pottery kebab and Hatay casserole with my wife, both were extraordinary! Portions and quality were top-notch. Absolutely the best dining spot in Göreme.',
    'about_quote_author_tr' => 'Müşteri Değerlendirmesi · Tripadvisor',
    'about_quote_author_en' => 'Guest Review · Tripadvisor',
    'about_rating' => '5.0',
    'about_image_1' => '/assets/images/about-1.jpg',
    'about_image_2' => '/assets/images/about-2.jpg',
];

if (is_post()) {
    verify_csrf();
    try {
        $q = $pdo->prepare('REPLACE INTO settings(`key`,`value`) VALUES(?,?)');

        foreach ($fields as $key => $label) {
            $val = trim((string)($_POST[$key] ?? ''));
            $q->execute([$key, $val]);
        }

        foreach (['about_image_1', 'about_image_2'] as $key) {
            $current = setting($pdo, $key, $defaults[$key] ?? '');
            $new = upload_site_image($_FILES[$key] ?? [], $config);
            if ($new) {
                if ($current && !in_array($current, ['/assets/images/about-1.jpg', '/assets/images/about-2.jpg'], true)) {
                    delete_site_image($current);
                }
                $q->execute([$key, $new]);
            } elseif (isset($_POST['remove_' . $key])) {
                if ($current && !in_array($current, ['/assets/images/about-1.jpg', '/assets/images/about-2.jpg'], true)) {
                    delete_site_image($current);
                }
                $q->execute([$key, '']);
            }
        }

        flash('ok', 'Hakkımızda sayfası ve hikâye bilgileri başarıyla kaydedildi.');
        redirect('/admin/about.php');
    } catch (Throwable $e) {
        flash('err', 'Kaydetme hatası: ' . $e->getMessage());
        redirect('/admin/about.php');
    }
}

$pageTitle = 'Hakkımızda Sayfası';
$active = 'about';
include __DIR__ . '/_top.php';
?>

<div class="head">
  <div>
    <h1>Hakkımızda Sayfası Yönetimi</h1>
    <p>QR karşılama ekranındaki küçük pencere ve <strong>/hakkimizda.php</strong> sayfasındaki metin, görsel ve yorumları buradan düzenleyebilirsiniz.</p>
  </div>
  <div class="actions">
    <a class="btn" href="<?=e(base_url('/hakkimizda.php'))?>" target="_blank">Canlı Sayfayı Gör ↗</a>
    <a class="btn" href="<?=e(base_url('/'))?>" target="_blank">Karşılama Ekranı ↗</a>
  </div>
</div>

<div class="panel">
  <form class="form grid" method="post" enctype="multipart/form-data">
    <?=csrf_field()?>

    <!-- Bölüm 1: Karşılama ve Hikâye Metinleri -->
    <div class="full">
      <div class="panel-title" style="padding-left:0; font-size:16px; border-bottom:1px solid var(--line); padding-bottom:8px; margin-bottom:16px;">
        1. Karşılama ve Hikâye Metinleri
      </div>
    </div>

    <label>
      <?=$fields['about_subtitle_tr']?>
      <input class="input" name="about_subtitle_tr" value="<?=e(setting($pdo, 'about_subtitle_tr', $defaults['about_subtitle_tr']))?>">
    </label>

    <label>
      <?=$fields['about_subtitle_en']?>
      <input class="input" name="about_subtitle_en" value="<?=e(setting($pdo, 'about_subtitle_en', $defaults['about_subtitle_en']))?>">
    </label>

    <label>
      <?=$fields['about_title_tr']?>
      <input class="input" name="about_title_tr" value="<?=e(setting($pdo, 'about_title_tr', $defaults['about_title_tr']))?>">
    </label>

    <label>
      <?=$fields['about_title_en']?>
      <input class="input" name="about_title_en" value="<?=e(setting($pdo, 'about_title_en', $defaults['about_title_en']))?>">
    </label>

    <label class="full">
      <?=$fields['about_text_tr']?>
      <textarea class="input" name="about_text_tr" rows="5" style="resize:vertical"><?=e(setting($pdo, 'about_text_tr', $defaults['about_text_tr']))?></textarea>
      <small style="color:var(--muted); display:block; margin-top:4px;">Paragraflar için bir satır boşluk bırakabilirsiniz.</small>
    </label>

    <label class="full">
      <?=$fields['about_text_en']?>
      <textarea class="input" name="about_text_en" rows="5" style="resize:vertical"><?=e(setting($pdo, 'about_text_en', $defaults['about_text_en']))?></textarea>
      <small style="color:var(--muted); display:block; margin-top:4px;">Enter a blank line between paragraphs for English.</small>
    </label>

    <label>
      <?=$fields['about_sign_tr']?>
      <input class="input" name="about_sign_tr" value="<?=e(setting($pdo, 'about_sign_tr', $defaults['about_sign_tr']))?>">
    </label>

    <label>
      <?=$fields['about_sign_en']?>
      <input class="input" name="about_sign_en" value="<?=e(setting($pdo, 'about_sign_en', $defaults['about_sign_en']))?>">
    </label>

    <!-- Bölüm 2: Tripadvisor & Misafir Değerlendirmesi -->
    <div class="full" style="margin-top:20px;">
      <div class="panel-title" style="padding-left:0; font-size:16px; border-bottom:1px solid var(--line); padding-bottom:8px; margin-bottom:16px;">
        2. Tripadvisor / Misafir Değerlendirmesi
      </div>
    </div>

    <label>
      <?=$fields['about_quote_title_tr']?>
      <input class="input" name="about_quote_title_tr" value="<?=e(setting($pdo, 'about_quote_title_tr', $defaults['about_quote_title_tr']))?>">
    </label>

    <label>
      <?=$fields['about_quote_title_en']?>
      <input class="input" name="about_quote_title_en" value="<?=e(setting($pdo, 'about_quote_title_en', $defaults['about_quote_title_en']))?>">
    </label>

    <label class="full">
      <?=$fields['about_quote_tr']?>
      <textarea class="input" name="about_quote_tr" rows="3" style="resize:vertical"><?=e(setting($pdo, 'about_quote_tr', $defaults['about_quote_tr']))?></textarea>
    </label>

    <label class="full">
      <?=$fields['about_quote_en']?>
      <textarea class="input" name="about_quote_en" rows="3" style="resize:vertical"><?=e(setting($pdo, 'about_quote_en', $defaults['about_quote_en']))?></textarea>
    </label>

    <label>
      <?=$fields['about_quote_author_tr']?>
      <input class="input" name="about_quote_author_tr" value="<?=e(setting($pdo, 'about_quote_author_tr', $defaults['about_quote_author_tr']))?>">
    </label>

    <label>
      <?=$fields['about_quote_author_en']?>
      <input class="input" name="about_quote_author_en" value="<?=e(setting($pdo, 'about_quote_author_en', $defaults['about_quote_author_en']))?>">
    </label>

    <label>
      <?=$fields['about_rating']?>
      <input class="input" name="about_rating" value="<?=e(setting($pdo, 'about_rating', $defaults['about_rating']))?>">
    </label>

    <!-- Bölüm 3: Hakkımızda Görselleri -->
    <div class="full" style="margin-top:20px;">
      <div class="panel-title" style="padding-left:0; font-size:16px; border-bottom:1px solid var(--line); padding-bottom:8px; margin-bottom:16px;">
        3. Hakkımızda Görselleri (Mekân, Taş Fırın & Lezzetler)
      </div>
    </div>

    <?php 
    $images = [
        'about_image_1' => [
            'label' => '1. Görsel (Taş Fırın & Testi Kebabı)',
            'def' => $defaults['about_image_1'],
        ],
        'about_image_2' => [
            'label' => '2. Görsel (Mekân & Sunum Fotoğrafı)',
            'def' => $defaults['about_image_2'],
        ],
    ];
    foreach ($images as $key => $item):
        $cur = setting($pdo, $key, $item['def']);
    ?>
    <label>
      <?=$item['label']?>
      <input class="input" type="file" name="<?=e($key)?>" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
      <?php if ($cur): ?>
        <div style="margin-top:10px;">
          <img class="preview" src="<?=e(image_url($cur))?>" style="max-height:140px; border-radius:8px; border:1px solid var(--line);" alt="">
          <span class="checks" style="margin-top:6px; display:inline-block;">
            <label><input type="checkbox" name="remove_<?=e($key)?>"> Bu görseli kaldır</label>
          </span>
        </div>
      <?php endif; ?>
    </label>
    <?php endforeach; ?>

    <div class="full" style="margin-top:24px; padding-top:16px; border-top:1px solid var(--line);">
      <button class="btn primary" type="submit" style="padding:12px 28px; font-weight:700;">Hakkımızda Bilgilerini Kaydet</button>
    </div>
  </form>
</div>

<?php include __DIR__ . '/_bottom.php'; ?>
