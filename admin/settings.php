<?php
require __DIR__ . '/../app/bootstrap.php'; require_admin(); require_once __DIR__.'/../app/upload.php';
$textFields=[
'restaurant_name'=>'Restoran Adı','tagline_tr'=>'Slogan TR','tagline_en'=>'Slogan EN',
'hero_title_tr'=>'Hero Başlık TR','hero_title_en'=>'Hero Başlık EN',
'announcement_tr'=>'Üst Bant TR','announcement_en'=>'Üst Bant EN',
'address'=>'Adres','phone'=>'Telefon','email'=>'E-posta','hours_tr'=>'Çalışma Saati TR','hours_en'=>'Çalışma Saati EN',
'whatsapp'=>'WhatsApp (ülke kodu + numara)','instagram'=>'Instagram URL','maps_url'=>'Google Maps URL','price_range'=>'Google fiyat aralığı'
];
if(is_post()){
 verify_csrf();
 try{
  $q=$pdo->prepare('INSERT INTO settings(`key`,`value`) VALUES(?,?) ON DUPLICATE KEY UPDATE value=VALUES(value)');
  foreach($textFields as $key=>$label) $q->execute([$key,trim((string)($_POST[$key]??''))]);
  foreach(['service_outdoor','service_fireplace','service_private_room'] as $key) $q->execute([$key,isset($_POST[$key])?'1':'0']);
  foreach(['logo_image','hero_image','story_image'] as $key){
    $current=setting($pdo,$key,'');
    $new=upload_site_image($_FILES[$key]??[],$config);
    if($new){delete_site_image($current);$q->execute([$key,$new]);}
    elseif(isset($_POST['remove_'.$key])){delete_site_image($current);$q->execute([$key,'']);}
  }
  flash('ok','Site ayarları kaydedildi.');redirect('/admin/settings.php');
 }catch(Throwable $e){flash('err',$e->getMessage());redirect('/admin/settings.php');}
}
$pageTitle='Site Ayarları';$active='settings';include __DIR__.'/_top.php';
?>
<div class="head"><div><h1>Site Ayarları</h1><p>İletişim, ana sayfa görselleri ve hizmetler.</p></div></div>
<div class="panel"><form class="form grid" method="post" enctype="multipart/form-data"><?=csrf_field()?>
<?php foreach($textFields as $key=>$label):?>
<label class="<?=in_array($key,['tagline_tr','tagline_en','hero_title_tr','hero_title_en','announcement_tr','announcement_en','address'])?'full':''?>"><?=$label?>
<input class="input" name="<?=e($key)?>" value="<?=e(setting($pdo,$key))?>"></label>
<?php endforeach;?>
<div class="full"><div class="panel-title" style="padding-left:0">Marka ve Karşılama Görselleri</div></div>
<?php foreach(['logo_image'=>'Kebapzade Logosu (şeffaf PNG önerilir)','hero_image'=>'Karşılama Arka Planı','story_image'=>'Hikâye Görseli'] as $key=>$label): $cur=setting($pdo,$key,'');?>
<label><?=$label?><input class="input" type="file" name="<?=e($key)?>" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
<?php if($cur):?><img class="preview" src="<?=e(image_url($cur))?>"><span class="checks"><label><input type="checkbox" name="remove_<?=e($key)?>"> Görseli kaldır</label></span><?php endif;?></label>
<?php endforeach;?>
<div class="full"><div class="panel-title" style="padding-left:0">Özellikler</div><div class="checks">
<label><input type="checkbox" name="service_outdoor" <?=checked(setting($pdo,'service_outdoor','1')==='1')?>> Açık hava bölümü</label>
<label><input type="checkbox" name="service_fireplace" <?=checked(setting($pdo,'service_fireplace','1')==='1')?>> Şömine</label>
<label><input type="checkbox" name="service_private_room" <?=checked(setting($pdo,'service_private_room','1')==='1')?>> Özel yemek odası</label>
</div></div>
<div class="full"><button class="btn primary">Ayarları Kaydet</button></div>
</form></div>
<?php include __DIR__.'/_bottom.php'; ?>
