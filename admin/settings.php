<?php
require __DIR__ . '/../app/bootstrap.php'; require_admin(); require_once __DIR__.'/../app/upload.php';
$textFields=[
'restaurant_name'=>'Restoran Adı','tagline_tr'=>'Slogan TR','tagline_en'=>'Slogan EN',
'hero_title_tr'=>'Hero Başlık TR','hero_title_en'=>'Hero Başlık EN',
'announcement_tr'=>'Üst Bant TR','announcement_en'=>'Üst Bant EN',
'address'=>'Adres','phone'=>'Telefon','email'=>'E-posta','hours_tr'=>'Çalışma Saati TR','hours_en'=>'Çalışma Saati EN',
'whatsapp'=>'WhatsApp (ülke kodu + numara)','instagram'=>'Instagram URL','maps_url'=>'Google Maps URL','price_range'=>'Google fiyat aralığı'
];
$fieldDefaults=[
 'instagram'=>'https://www.instagram.com/kebapzaderestaurant/',
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
  clear_menu_cache();
  flash('ok','Site ayarları kaydedildi.');redirect('/admin/settings.php');
 }catch(Throwable $e){flash('err',$e->getMessage());redirect('/admin/settings.php');}
}
$pageTitle='Site Ayarları';$active='settings';include __DIR__.'/_top.php';
?>
<div class="head"><div><h1>Site Ayarları</h1><p>İletişim, ana sayfa görselleri, çalışma saatleri ve hizmetler.</p></div><a class="btn" href="<?=e(base_url('/admin/about.php'))?>">Hakkımızda Sayfası Yönetimi →</a></div>

<div class="help-banner">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
  <div><strong>Görsel Yükleme İpuçları</strong>Logo için şeffaf PNG kullanın. Arka plan görseli için en az 1920×1080 piksel yatay fotoğraf önerilir. Dosyaları aşağıdaki alanlara sürükleyip bırakabilirsiniz.</div>
</div>

<div class="panel"><form class="form grid" method="post" enctype="multipart/form-data"><?=csrf_field()?>
<?php foreach($textFields as $key=>$label): $fieldValue=setting($pdo,$key,$fieldDefaults[$key]??''); if($fieldValue==='' && isset($fieldDefaults[$key])) $fieldValue=$fieldDefaults[$key];?>
<label class="<?=in_array($key,['tagline_tr','tagline_en','hero_title_tr','hero_title_en','announcement_tr','announcement_en','address'])?'full':''?>"><?=$label?>
<input class="input" name="<?=e($key)?>" value="<?=e($fieldValue)?>"></label>
<?php endforeach;?>

<div class="full"><div class="panel-title" style="padding-left:0;margin-top:8px">Marka ve Karşılama Görselleri</div></div>

<?php
$imageFields = [
  'logo_image' => ['label'=>'Kebapzade Logosu (şeffaf PNG önerilir)', 'hint'=>'Şeffaf arka planlı PNG, maks. 4 MB'],
  'hero_image' => ['label'=>'Karşılama Arka Planı', 'hint'=>'Yatay fotoğraf, en az 1920×1080 px önerilir'],
  'story_image' => ['label'=>'Hikâye Görseli', 'hint'=>'Restoran veya yemek fotoğrafı, yatay format önerilir'],
];
$imgIdx = 0;
foreach($imageFields as $key=>$meta): $cur=setting($pdo,$key,''); $imgIdx++;
?>
<div class="full">
  <label style="margin-bottom:4px"><?=$meta['label']?></label>
  <div class="upload-zone" id="uploadZone<?=$imgIdx?>">
    <input type="file" name="<?=e($key)?>" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" id="imageInput<?=$imgIdx?>">
    <div class="upload-zone-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
    </div>
    <div class="upload-zone-text">Görseli buraya sürükleyin veya tıklayın</div>
    <div class="upload-zone-hint"><?=$meta['hint']?></div>
    <div class="upload-zone-filename" id="fileName<?=$imgIdx?>"></div>
  </div>

  <div class="upload-preview-wrap" id="previewWrap<?=$imgIdx?>" style="display:none">
    <img id="previewImg<?=$imgIdx?>" src="" alt="Önizleme">
    <div class="upload-preview-info">
      <p class="file-name" id="previewName<?=$imgIdx?>"></p>
      <p class="file-meta" id="previewMeta<?=$imgIdx?>"></p>
      <div class="upload-preview-actions">
        <button type="button" class="btn sm danger" onclick="clearUploadN(<?=$imgIdx?>)">Kaldır</button>
      </div>
    </div>
  </div>

  <?php if($cur):?>
  <div class="existing-image-wrap" id="existingWrap<?=$imgIdx?>">
    <img src="<?=e(image_url($cur))?>" alt="<?=$meta['label']?>">
    <div class="existing-image-info">
      <span class="label-tag">Mevcut Görsel</span>
      <p>Bu görsel şu anda sitede kullanılıyor.</p>
      <div class="checks">
        <label><input type="checkbox" name="remove_<?=e($key)?>" onchange="document.getElementById('existingWrap<?=$imgIdx?>').style.opacity=this.checked?'0.4':'1'"> Görseli kaldır</label>
      </div>
    </div>
  </div>
  <?php endif;?>
</div>
<?php endforeach;?>

<div class="full"><div class="panel-title" style="padding-left:0">Özellikler</div><div class="checks">
<label><input type="checkbox" name="service_outdoor" <?=checked(setting($pdo,'service_outdoor','1')==='1')?>> Açık hava bölümü</label>
<label><input type="checkbox" name="service_fireplace" <?=checked(setting($pdo,'service_fireplace','1')==='1')?>> Şömine</label>
<label><input type="checkbox" name="service_private_room" <?=checked(setting($pdo,'service_private_room','1')==='1')?>> Özel yemek odası</label>
</div></div>
<div class="full"><button class="btn primary">Ayarları Kaydet</button></div>
</form></div>

<script>
(function(){
  function initUploadZone(idx){
    var zone = document.getElementById('uploadZone'+idx);
    var input = document.getElementById('imageInput'+idx);
    var fileName = document.getElementById('fileName'+idx);
    var previewWrap = document.getElementById('previewWrap'+idx);
    var previewImg = document.getElementById('previewImg'+idx);
    var previewName = document.getElementById('previewName'+idx);
    var previewMeta = document.getElementById('previewMeta'+idx);

    if(!zone) return;

    ['dragenter','dragover'].forEach(function(ev){
      zone.addEventListener(ev, function(e){ e.preventDefault(); zone.classList.add('drag-over'); });
    });
    ['dragleave','drop'].forEach(function(ev){
      zone.addEventListener(ev, function(e){ e.preventDefault(); zone.classList.remove('drag-over'); });
    });

    zone.addEventListener('drop', function(e){
      var files = e.dataTransfer.files;
      if(files.length > 0){
        input.files = files;
        showPreview(files[0]);
      }
    });

    input.addEventListener('change', function(){
      if(input.files && input.files[0]) showPreview(input.files[0]);
    });

    function showPreview(file){
      var validTypes = ['image/jpeg','image/png','image/webp'];
      if(validTypes.indexOf(file.type) === -1){
        alert('Sadece JPG, PNG veya WEBP yükleyebilirsiniz.');
        input.value = '';
        return;
      }
      if(file.size > 4 * 1024 * 1024){
        alert('Dosya boyutu en fazla 4 MB olabilir. Seçtiğiniz dosya: ' + (file.size / 1024 / 1024).toFixed(1) + ' MB');
        input.value = '';
        return;
      }
      zone.classList.add('has-file');
      fileName.textContent = '✓ ' + file.name;
      previewName.textContent = file.name;
      var size = file.size < 1024*1024 ? (file.size/1024).toFixed(0)+' KB' : (file.size/1024/1024).toFixed(1)+' MB';
      previewMeta.textContent = size + ' · ' + file.type.split('/')[1].toUpperCase();
      var reader = new FileReader();
      reader.onload = function(e){
        previewImg.src = e.target.result;
        previewWrap.style.display = 'flex';
      };
      reader.readAsDataURL(file);
    }

    window['clearUploadN'] = window['clearUploadN'] || function(n){
      var i = document.getElementById('imageInput'+n);
      var z = document.getElementById('uploadZone'+n);
      var f = document.getElementById('fileName'+n);
      var p = document.getElementById('previewWrap'+n);
      if(i) i.value = '';
      if(z) z.classList.remove('has-file');
      if(f) f.textContent = '';
      if(p) p.style.display = 'none';
    };
  }

  for(var i=1;i<=<?=$imgIdx?>;i++) initUploadZone(i);
})();
</script>
<?php include __DIR__.'/_bottom.php'; ?>
