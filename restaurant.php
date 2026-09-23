<?php
require __DIR__ . '/app/bootstrap.php';

$L = lang();
$nameCol = $L === 'en' ? 'name_en' : 'name_tr';
$descCol = $L === 'en' ? 'description_en' : 'description_tr';
$labelCol = $L === 'en' ? 'label_en' : 'label_tr';

$cats = $pdo->query('SELECT * FROM categories WHERE is_active=1 ORDER BY sort_order,id')->fetchAll();
$itemQ = $pdo->prepare('SELECT * FROM items WHERE category_id=? AND is_active=1 ORDER BY sort_order,id');
$varQ = $pdo->prepare('SELECT * FROM item_variants WHERE item_id=? ORDER BY sort_order,id');

$restaurant = setting($pdo,'restaurant_name','Kapadokya Kebapzade Restaurant');
$heroTitle = setting($pdo, $L === 'en' ? 'hero_title_en' : 'hero_title_tr', 'Kapadokya Kebapzade');
$tagline = setting($pdo, $L === 'en' ? 'tagline_en' : 'tagline_tr');
$announcement = setting($pdo, $L === 'en' ? 'announcement_en' : 'announcement_tr');
$hours = setting($pdo, $L === 'en' ? 'hours_en' : 'hours_tr');
$address = setting($pdo,'address');
$phone = setting($pdo,'phone');
$email = setting($pdo,'email');
$whatsapp = preg_replace('/\D+/', '', setting($pdo,'whatsapp'));
$maps = setting($pdo,'maps_url');
$instagram = setting($pdo,'instagram');
$heroImage = image_url(setting($pdo,'hero_image'));
$storyImage = image_url(setting($pdo,'story_image'));
$reservationEnabled = setting($pdo,'reservation_enabled','1') === '1';
$reservationNotice = setting($pdo, $L === 'en' ? 'reservation_notice_en' : 'reservation_notice_tr');
$priceRange = setting($pdo,'price_range','₺₺');
$old = $_SESSION['reservation_old'] ?? [];
unset($_SESSION['reservation_old']);

$t = [
 'menu'=>$L==='en'?'Menu':'Menü','story'=>$L==='en'?'Our Story':'Hikâyemiz','contact'=>$L==='en'?'Contact':'İletişim',
 'services'=>$L==='en'?'Experience':'Deneyim','eyebrow'=>$L==='en'?'Göreme · Cappadocia':'Göreme · Kapadokya',
 'heroP'=>$L==='en'?'Traditional recipes, stone-oven cooking and charcoal fire. A contemporary presentation rooted in Anatolian culinary culture.':'Yöresel reçeteler, taş fırın ve kömür ateşi. Anadolu mutfağının köklü lezzetlerini çağdaş ve özenli bir sunumla buluşturuyoruz.',
 'viewMenu'=>$L==='en'?'Explore Menu':'Menüyü İncele','reserve'=>$L==='en'?'Book a Table':'Masa Ayırt','location'=>$L==='en'?'Location':'Konum',
 'hours'=>$L==='en'?'Opening Hours':'Çalışma Saatleri','menuTitle'=>$L==='en'?'The Kebapzade Menu':'Kebapzade Menüsü',
 'menuIntro'=>$L==='en'?'Explore regional flavours, grilled specialities, mezze, desserts and drinks.':'Yöresel yemeklerden kebaplara, mezelerden tatlı ve içeceklere Kebapzade seçkisini keşfedin.',
 'all'=>$L==='en'?'All':'Tümü','search'=>$L==='en'?'Search menu...':'Menüde ara...','featured'=>$L==='en'?'Signature':'Öne Çıkan',
 'veg'=>$L==='en'?'Vegetarian':'Vejetaryen','spicy'=>$L==='en'?'Spicy':'Acılı','storyTitle'=>$L==='en'?'A Table Shaped by Cappadocia':'Kapadokya ile Şekillenen Bir Sofra',
 'storyText'=>$L==='en'?'Since 2008, Kebapzade has focused on regional ingredients, traditional recipes and careful cooking. Pottery kebab, grills, mezze and local desserts are prepared with respect for their original character.':'2008’den bu yana Kebapzade; yöresel malzeme, geleneksel reçete ve özenli pişirme anlayışına odaklanıyor. Testi kebabı, ızgaralar, mezeler ve yöresel tatlılar kendi karakterine saygı duyularak hazırlanıyor.',
 'since'=>$L==='en'?'Since':'Kuruluş','experienceTitle'=>$L==='en'?'More Than a Meal':'Yalnızca Bir Yemek Değil',
 'experienceText'=>$L==='en'?'A warm Cappadocian atmosphere for relaxed dinners, celebrations and private gatherings.':'Rahat akşam yemekleri, kutlamalar ve özel buluşmalar için Kapadokya’nın sıcak atmosferi.',
 'outdoor'=>$L==='en'?'Outdoor seating':'Açık hava bölümü','fireplace'=>$L==='en'?'Fireplace':'Şömine','private'=>$L==='en'?'Private dining room':'Özel yemek odası',
 'reservationTitle'=>$L==='en'?'Reserve Your Table':'Masanızı Ayırtın','reservationText'=>$L==='en'?'Send your preferred date and time. Our team will contact you to confirm availability.':'Tarih ve saatinizi gönderin. Ekibimiz müsaitliği kontrol ederek rezervasyonunuzu kesinleştirmek için sizinle iletişime geçsin.',
 'name'=>$L==='en'?'Name & Surname':'Ad Soyad','phone'=>$L==='en'?'Phone':'Telefon','email'=>$L==='en'?'Email (optional)':'E-posta (opsiyonel)',
 'date'=>$L==='en'?'Date':'Tarih','time'=>$L==='en'?'Time':'Saat','guests'=>$L==='en'?'Guests':'Kişi','note'=>$L==='en'?'Note':'Not',
 'send'=>$L==='en'?'Send Reservation Request':'Rezervasyon Talebi Gönder','call'=>$L==='en'?'Call':'Ara','whatsapp'=>'WhatsApp',
 'allergen'=>$L==='en'?'Please inform our team about food allergies or special dietary requirements.':'Gıda alerjiniz veya özel beslenme ihtiyacınız varsa lütfen ekibimizi bilgilendirin.'
];

$base = rtrim(base_url(''), '/');
if ($base === '') {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $base = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
}
$canonical = $base . '/';
$schema = [
 '@context'=>'https://schema.org','@type'=>'Restaurant','name'=>$restaurant,'url'=>$canonical,'telephone'=>$phone,
 'email'=>$email,'priceRange'=>$priceRange,'servesCuisine'=>['Turkish','Anatolian','Kebab'],
 'address'=>['@type'=>'PostalAddress','streetAddress'=>$address,'addressLocality'=>'Göreme','addressRegion'=>'Nevşehir','addressCountry'=>'TR']
];
if ($instagram) $schema['sameAs']=[$instagram];
if ($heroImage) $schema['image']=$heroImage;
?>
<!doctype html>
<html lang="<?=$L?>">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="theme-color" content="#0d0b09">
<title><?=e($restaurant)?> | Göreme, Cappadocia</title><meta name="description" content="<?=e($tagline)?>"><link rel="canonical" href="<?=e($canonical)?>">
<meta property="og:type" content="restaurant"><meta property="og:title" content="<?=e($restaurant)?>"><meta property="og:description" content="<?=e($tagline)?>"><meta property="og:url" content="<?=e($canonical)?>">
<?php if($heroImage):?><meta property="og:image" content="<?=e($heroImage)?>"><?php endif;?>
<link rel="stylesheet" href="<?=e(base_url('/assets/css/site.css'))?>"><script type="application/ld+json"><?=json_encode($schema,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)?></script>
</head>
<body>
<div class="topbar"><div class="wrap"><span><?=e($announcement)?></span><span><?=e($phone)?></span></div></div>
<nav class="nav"><div class="wrap nav-in"><a class="brand" href="<?=e(base_url('/'))?>"><span class="brand-mark">K</span><span>Kebapzade</span></a>
<div class="navlinks"><a href="#menu"><?=$t['menu']?></a><a href="#experience"><?=$t['services']?></a><a href="#story"><?=$t['story']?></a><?php if($reservationEnabled):?><a href="#reservation"><?=$t['reserve']?></a><?php endif;?><a href="#contact"><?=$t['contact']?></a></div>
<div class="langs"><a class="<?=$L==='tr'?'active':''?>" href="?lang=tr">TR</a><a class="<?=$L==='en'?'active':''?>" href="?lang=en">EN</a></div></div></nav>

<header class="hero <?= $heroImage ? 'has-photo' : '' ?>" <?php if($heroImage):?>style="--hero-image:url('<?=e($heroImage)?>')"<?php endif;?>>
 <div class="wrap hero-content"><div class="eyebrow"><?=$t['eyebrow']?></div><h1><?=e($heroTitle)?></h1><p><?=e($t['heroP'])?></p>
 <div class="actions"><a class="btn btn-primary" href="#menu"><?=$t['viewMenu']?> ↓</a><?php if($reservationEnabled):?><a class="btn" href="#reservation"><?=$t['reserve']?></a><?php elseif($phone):?><a class="btn" href="tel:<?=e(preg_replace('/[^\d+]/','',$phone))?>"><?=$t['call']?></a><?php endif;?></div></div>
</header>

<div class="quick"><div class="wrap quick-grid">
<a class="quick-item" <?= $maps ? 'href="'.e($maps).'" target="_blank" rel="noopener"' : ''?>><small><?=$t['location']?></small><strong><?=e($address)?></strong><span>Göreme · Cappadocia</span></a>
<div class="quick-item"><small><?=$t['hours']?></small><strong><?=e($hours)?></strong><span><?=e($phone)?></span></div>
<a class="quick-item" href="<?= $reservationEnabled?'#reservation':('tel:'.e(preg_replace('/[^\d+]/','',$phone))) ?>"><small><?=e($t['reserve'])?></small><strong><?=e($restaurant)?></strong><span><?=e($tagline)?></span></a>
</div></div>

<section class="section" id="menu"><div class="wrap"><div class="section-head"><div><div class="eyebrow">Kebapzade</div><h2><?=$t['menuTitle']?></h2><p class="section-intro"><?=$t['menuIntro']?></p></div></div></div>
<div class="menu-tools"><div class="wrap menu-row"><input class="search" data-search type="search" placeholder="<?=e($t['search'])?>" aria-label="<?=e($t['search'])?>"><div class="chips"><button class="chip active" data-filter="all"><?=$t['all']?></button><?php foreach($cats as $c): ?><button class="chip" data-filter="<?=e($c['slug'])?>"><?=e($c[$nameCol] ?: $c['name_tr'])?></button><?php endforeach; ?></div></div></div>
<div class="wrap" style="padding-top:45px">
<?php foreach($cats as $cat): $itemQ->execute([$cat['id']]); $menuItems=$itemQ->fetchAll(); ?>
<section class="category" id="cat-<?=e($cat['slug'])?>" data-category="<?=e($cat['slug'])?>"><div class="cat-head"><h3><?=e($cat[$nameCol] ?: $cat['name_tr'])?></h3><small><?=count($menuItems)?> <?=$L==='en'?'items':'ürün'?></small></div>
<?php if(!$menuItems): ?><div class="empty"><?=$L==='en'?'No active items in this category.':'Bu kategoride aktif ürün bulunmuyor.'?></div><?php endif;?><div class="items">
<?php foreach($menuItems as $item): $varQ->execute([$item['id']]);$variants=$varQ->fetchAll();$iname=$item[$nameCol]?:$item['name_tr'];$idesc=$item[$descCol]?:$item['description_tr'];$searchText=$item['name_tr'].' '.$item['name_en'].' '.$item['description_tr'].' '.$item['description_en'];$img=image_url($item['image_path']); ?>
<article class="item <?=$img?'with-image':''?>" data-item data-search="<?=e($searchText)?>"><?php if($img):?><img class="item-img" src="<?=e($img)?>" alt="<?=e($iname)?>" loading="lazy"><?php endif;?><div><h4><?=e($iname)?></h4><?php if($idesc):?><p><?=e($idesc)?></p><?php endif;?><div class="badges"><?php if($item['is_featured']):?><span class="badge featured"><?=$t['featured']?></span><?php endif;?><?php if($item['is_vegetarian']):?><span class="badge veg"><?=$t['veg']?></span><?php endif;?><?php if($item['is_spicy']):?><span class="badge spicy"><?=$t['spicy']?></span><?php endif;?></div>
<?php foreach($variants as $v):?><div class="variant"><span><?=e($v[$labelCol] ?: $v['label_tr'])?></span><b><?=e(money($v['price']))?></b></div><?php endforeach;?></div><div class="price"><?=e(money($item['price']))?><?php $noteCol=$L==='en'?'price_note_en':'price_note_tr'; if($item[$noteCol]):?><div style="font:11px Inter,sans-serif;color:#8f8379;margin-top:5px"><?=e($item[$noteCol])?></div><?php endif;?></div></article>
<?php endforeach;?></div></section><?php endforeach;?></div></section>

<section class="section experience" id="experience"><div class="wrap"><div class="section-head"><div><div class="eyebrow">Kebapzade</div><h2><?=$t['experienceTitle']?></h2><p class="section-intro"><?=$t['experienceText']?></p></div></div>
<div class="service-grid">
<?php if(setting($pdo,'service_outdoor','1')==='1'):?><div class="service-card"><span>01</span><h3><?=$t['outdoor']?></h3><p><?=$L==='en'?'Enjoy Cappadocia evenings in our outdoor seating area.':'Kapadokya akşamlarını açık hava bölümümüzde keyifle yaşayın.'?></p></div><?php endif;?>
<?php if(setting($pdo,'service_fireplace','1')==='1'):?><div class="service-card"><span>02</span><h3><?=$t['fireplace']?></h3><p><?=$L==='en'?'A warm atmosphere around the fireplace on cooler evenings.':'Serin akşamlarda şömine çevresinde sıcak ve samimi bir atmosfer.'?></p></div><?php endif;?>
<?php if(setting($pdo,'service_private_room','1')==='1'):?><div class="service-card"><span>03</span><h3><?=$t['private']?></h3><p><?=$L==='en'?'A more private setting for families, groups and celebrations.':'Aileler, gruplar ve özel kutlamalar için daha mahrem bir yemek alanı.'?></p></div><?php endif;?>
</div></div></section>

<section class="section about" id="story"><div class="wrap about-grid"><div class="story-card <?= $storyImage?'has-photo':'' ?>" <?php if($storyImage):?>style="--story-image:url('<?=e($storyImage)?>')"<?php endif;?>><div class="seal"><?=$t['since']?><br><strong>2008</strong><br>Göreme</div></div><div><div class="eyebrow">Kebapzade · Göreme</div><h2><?=$t['storyTitle']?></h2><p class="section-intro"><?=$t['storyText']?></p><div class="actions"><a class="btn btn-primary" href="#contact"><?=$t['contact']?></a></div></div></div></section>

<?php if($reservationEnabled):?>
<section class="section reservation-section" id="reservation"><div class="wrap reservation-grid"><div><div class="eyebrow"><?=$t['reserve']?></div><h2><?=$t['reservationTitle']?></h2><p class="section-intro"><?=$t['reservationText']?></p><div class="reservation-note"><?=e($reservationNotice)?></div><?php if($phone):?><p class="reservation-phone"><span><?=$t['call']?></span><a href="tel:<?=e(preg_replace('/[^\d+]/','',$phone))?>"><?=e($phone)?></a></p><?php endif;?></div>
<form class="reservation-form" method="post" action="<?=e(base_url('/reservation.php'))?>"><?=csrf_field()?><input type="hidden" name="language" value="<?=$L?>"><input class="hp" tabindex="-1" autocomplete="off" name="website" aria-hidden="true">
<?php if($m=flash('reservation_ok')):?><div class="form-alert success"><?=e($m)?></div><?php endif;?><?php if($m=flash('reservation_err')):?><div class="form-alert error"><?=e($m)?></div><?php endif;?>
<div class="reserve-fields"><label><?=$t['name']?><input required maxlength="160" name="guest_name" value="<?=e($old['guest_name']??'')?>"></label><label><?=$t['phone']?><input required inputmode="tel" maxlength="60" name="phone" value="<?=e($old['phone']??'')?>"></label><label><?=$t['email']?><input type="email" maxlength="190" name="email" value="<?=e($old['email']??'')?>"></label><label><?=$t['guests']?><select name="guest_count"><?php for($i=1;$i<=20;$i++):?><option value="<?=$i?>" <?=selected($old['guest_count']??2,$i)?>><?=$i?></option><?php endfor;?></select></label><label><?=$t['date']?><input required type="date" name="reservation_date" min="<?=date('Y-m-d')?>" max="<?=date('Y-m-d',strtotime('+120 days'))?>" value="<?=e($old['reservation_date']??'')?>"></label><label><?=$t['time']?><input required type="time" name="reservation_time" min="10:00" max="22:30" step="900" value="<?=e($old['reservation_time']??'')?>"></label><label class="full"><?=$t['note']?><textarea name="note" maxlength="2000" placeholder="<?=$L==='en'?'Celebration, high chair, dietary request...':'Kutlama, çocuk sandalyesi, beslenme talebi...'?>"><?=e($old['note']??'')?></textarea></label></div><button class="btn btn-primary reserve-submit"><?=$t['send']?></button></form>
</div></section><?php endif;?>

<footer class="footer" id="contact"><div class="wrap"><div class="foot-grid"><div><div class="brand"><span class="brand-mark">K</span><span>Kebapzade</span></div><p style="max-width:430px"><?=e($tagline)?></p><p class="allergen"><?=e($t['allergen'])?></p></div><div><h4><?=$t['contact']?></h4><p><?=e($address)?><br><?=e($phone)?><br><?=e($email)?></p></div><div><h4><?=$L==='en'?'Links':'Bağlantılar'?></h4><p><?php if($whatsapp):?><a target="_blank" rel="noopener" href="https://wa.me/<?=e($whatsapp)?>">WhatsApp</a><br><?php endif;?><?php if($maps):?><a target="_blank" rel="noopener" href="<?=e($maps)?>">Google Maps</a><br><?php endif;?><?php if($instagram):?><a target="_blank" rel="noopener" href="<?=e($instagram)?>">Instagram</a><br><?php endif;?><a href="<?=e(base_url('/qr-menu.php'))?>">QR Menu</a></p></div></div><div class="copyright"><span>© <?=date('Y')?> <?=e($restaurant)?></span><span>Göreme · Nevşehir</span></div></div></footer>

<div class="mobile-actions"><?php if($phone):?><a href="tel:<?=e(preg_replace('/[^\d+]/','',$phone))?>"><?=$t['call']?></a><?php endif;?><?php if($whatsapp):?><a target="_blank" rel="noopener" href="https://wa.me/<?=e($whatsapp)?>">WhatsApp</a><?php endif;?><?php if($reservationEnabled):?><a class="primary" href="#reservation"><?=$t['reserve']?></a><?php endif;?></div>
<?php if($whatsapp):?><a class="wa-float" target="_blank" rel="noopener" href="https://wa.me/<?=e($whatsapp)?>" aria-label="WhatsApp">WA</a><?php endif;?>
<script src="<?=e(base_url('/assets/js/site.js'))?>"></script></body></html>
