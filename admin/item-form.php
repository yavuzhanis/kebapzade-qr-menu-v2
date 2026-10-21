<?php
require __DIR__ . '/../app/bootstrap.php'; require_admin();
require_once __DIR__ . '/../app/upload.php';
$id=(int)($_GET['id']??0);
$row=['id'=>0,'category_id'=>'','name_tr'=>'','name_en'=>'','description_tr'=>'','description_en'=>'','price'=>'','price_note_tr'=>'','price_note_en'=>'','image_path'=>'','is_active'=>1,'is_featured'=>0,'is_vegetarian'=>0,'is_spicy'=>0,'sort_order'=>0];
if($id){$q=$pdo->prepare('SELECT * FROM items WHERE id=?');$q->execute([$id]);$row=$q->fetch()?:$row;}
$cats=$pdo->query('SELECT id,name_tr FROM categories ORDER BY sort_order,id')->fetchAll();
$variants=[];
if($id){$q=$pdo->prepare('SELECT * FROM item_variants WHERE item_id=? ORDER BY sort_order,id');$q->execute([$id]);$variants=$q->fetchAll();}
$error='';
if(is_post()){
 verify_csrf();
 $nameTr=trim($_POST['name_tr']??'');$category=(int)($_POST['category_id']??0);
 if($nameTr===''||!$category){$error='Kategori ve Türkçe ürün adı zorunludur.';}
 else{
  try{
   $img=$row['image_path'];
   $newImg=upload_menu_image($_FILES['image']??[], $config);
   if($newImg){delete_uploaded_image($img);$img=$newImg;}
   if(isset($_POST['remove_image'])&&$img){delete_uploaded_image($img);$img=null;}
   $price=trim($_POST['price']??'');$price=$price===''?null:str_replace(',','.',$price);
   $vals=[$category,$nameTr,trim($_POST['name_en']??''),trim($_POST['description_tr']??''),trim($_POST['description_en']??''),$price,trim($_POST['price_note_tr']??''),trim($_POST['price_note_en']??''),$img,isset($_POST['is_active'])?1:0,isset($_POST['is_featured'])?1:0,isset($_POST['is_vegetarian'])?1:0,isset($_POST['is_spicy'])?1:0,(int)($_POST['sort_order']??0)];
   if($id){
    $q=$pdo->prepare('UPDATE items SET category_id=?,name_tr=?,name_en=?,description_tr=?,description_en=?,price=?,price_note_tr=?,price_note_en=?,image_path=?,is_active=?,is_featured=?,is_vegetarian=?,is_spicy=?,sort_order=? WHERE id=?');
    $q->execute([...$vals,$id]);
   }else{
    $q=$pdo->prepare('INSERT INTO items(category_id,name_tr,name_en,description_tr,description_en,price,price_note_tr,price_note_en,image_path,is_active,is_featured,is_vegetarian,is_spicy,sort_order) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    $q->execute($vals);$id=(int)$pdo->lastInsertId();
   }

   $pdo->prepare('DELETE FROM item_variants WHERE item_id=?')->execute([$id]);
   $labelsTr=$_POST['variant_label_tr']??[];$labelsEn=$_POST['variant_label_en']??[];$prices=$_POST['variant_price']??[];
   $vs=$pdo->prepare('INSERT INTO item_variants(item_id,label_tr,label_en,price,sort_order) VALUES(?,?,?,?,?)');
   foreach($labelsTr as $i=>$lab){
     $lab=trim((string)$lab); if($lab==='') continue;
     $vp=trim((string)($prices[$i]??''));$vp=$vp===''?null:str_replace(',','.',$vp);
     $vs->execute([$id,$lab,trim((string)($labelsEn[$i]??'')),$vp,($i+1)*10]);
   }
   clear_menu_cache();
   flash('ok','Ürün kaydedildi.');redirect('/admin/items.php');
  }catch(Throwable $e){$error=$e->getMessage();}
 }
}
$pageTitle=$id?'Ürün Düzenle':'Ürün Ekle';$active='items';include __DIR__.'/_top.php';
?>
<div class="head"><div><h1><?=e($pageTitle)?></h1><p>İçerik, fiyat, görsel ve porsiyon seçenekleri.</p></div><a class="btn" href="<?=e(base_url('/admin/items.php'))?>">← Geri</a></div>

<div class="help-banner">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
  <div><strong>Görsel Yükleme Rehberi</strong>Ürün fotoğrafını aşağıdaki alana sürükleyip bırakabilir veya tıklayarak seçebilirsiniz. JPG, PNG veya WEBP formatında, en fazla 4 MB boyutunda olmalıdır.</div>
</div>

<?php if($error):?><div class="notice err"><?=e($error)?></div><?php endif;?>
<div class="panel"><form class="form grid" method="post" enctype="multipart/form-data"><?=csrf_field()?>
<label>Kategori<select name="category_id" required><option value="">Seçiniz</option><?php foreach($cats as $c):?><option value="<?=$c['id']?>" <?=selected($_POST['category_id']??$row['category_id'],$c['id'])?>><?=e($c['name_tr'])?></option><?php endforeach;?></select></label>
<label>Sıra<input class="input" type="number" name="sort_order" value="<?=e((string)($_POST['sort_order']??$row['sort_order']))?>"></label>
<label>Türkçe Ürün Adı<input class="input" name="name_tr" required value="<?=e($_POST['name_tr']??$row['name_tr'])?>"></label>
<label>İngilizce Ürün Adı<input class="input" name="name_en" value="<?=e($_POST['name_en']??$row['name_en'])?>"></label>
<label class="full">Türkçe Açıklama<textarea name="description_tr"><?=e($_POST['description_tr']??$row['description_tr'])?></textarea></label>
<label class="full">İngilizce Açıklama<textarea name="description_en"><?=e($_POST['description_en']??$row['description_en'])?></textarea></label>
<label>Tek Fiyat (₺)<input class="input" inputmode="decimal" name="price" value="<?=e((string)($_POST['price']??$row['price']))?>" placeholder="örn. 450"></label>
<label>Fiyat Notu TR<input class="input" name="price_note_tr" value="<?=e($_POST['price_note_tr']??$row['price_note_tr'])?>" placeholder="örn. kişi başı"></label>
<label>Fiyat Notu EN<input class="input" name="price_note_en" value="<?=e($_POST['price_note_en']??$row['price_note_en'])?>"></label>

<!-- Gelişmiş Görsel Yükleme Alanı -->
<div class="full">
  <label style="margin-bottom:4px">Ürün Görseli</label>
  <div class="upload-zone" id="uploadZone">
    <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" id="imageInput">
    <div class="upload-zone-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
    </div>
    <div class="upload-zone-text">Görseli buraya sürükleyin veya tıklayın</div>
    <div class="upload-zone-hint">JPG, PNG veya WEBP · Maks. 4 MB</div>
    <div class="upload-zone-filename" id="fileName"></div>
  </div>
  <div class="upload-tips">
    <span class="upload-tip">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      Kare veya yatay fotoğraf önerilir
    </span>
    <span class="upload-tip">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      Minimum 600×400 piksel
    </span>
  </div>

  <!-- Yeni seçilen dosya önizleme -->
  <div class="upload-preview-wrap" id="previewWrap" style="display:none">
    <img id="previewImg" src="" alt="Önizleme">
    <div class="upload-preview-info">
      <p class="file-name" id="previewName"></p>
      <p class="file-meta" id="previewMeta"></p>
      <div class="upload-preview-actions">
        <button type="button" class="btn sm danger" onclick="clearUpload()">Kaldır</button>
      </div>
    </div>
  </div>
</div>

<?php if($row['image_path']):?>
<div class="full">
  <div class="existing-image-wrap" id="existingWrap">
    <img src="<?=e(image_url($row['image_path']))?>" alt="Mevcut görsel">
    <div class="existing-image-info">
      <span class="label-tag">Mevcut Görsel</span>
      <p>Bu görsel şu anda ürün için kullanılıyor. Kaldırmak için alttaki kutuyu işaretleyin veya yeni bir görsel yükleyin.</p>
      <div class="checks">
        <label><input type="checkbox" name="remove_image" onchange="toggleExistingPreview(this)"> Mevcut görseli kaldır</label>
      </div>
    </div>
  </div>
</div>
<?php endif;?>

<div class="full"><strong style="font-size:13px">Porsiyon / fiyat seçenekleri</strong><p class="muted" style="font-size:12px">Karışık ızgara gibi 2 kişi / 3 kişi / 4 kişi seçenekleri için kullanın.</p>
<div id="variants">
<?php
$renderVariants=$variants ?: [['label_tr'=>'','label_en'=>'','price'=>'']];
foreach($renderVariants as $v):?>
<div class="variant-row">
<input class="input" style="margin:0" name="variant_label_tr[]" placeholder="TR: 2 Kişi" value="<?=e($v['label_tr']??'')?>">
<input class="input" style="margin:0" name="variant_label_en[]" placeholder="EN: 2 Persons" value="<?=e($v['label_en']??'')?>">
<input class="input" style="margin:0" name="variant_price[]" placeholder="Fiyat" value="<?=e((string)($v['price']??''))?>">
</div>
<?php endforeach;?>
</div><button class="btn sm" type="button" onclick="addVariant()">+ Satır Ekle</button></div>
<div class="checks full">
<label><input type="checkbox" name="is_active" <?=checked(isset($_POST['name_tr'])?isset($_POST['is_active']):$row['is_active'])?>> Yayında</label>
<label><input type="checkbox" name="is_featured" <?=checked(isset($_POST['name_tr'])?isset($_POST['is_featured']):$row['is_featured'])?>> Öne çıkan</label>
<label><input type="checkbox" name="is_vegetarian" <?=checked(isset($_POST['name_tr'])?isset($_POST['is_vegetarian']):$row['is_vegetarian'])?>> Vejetaryen</label>
<label><input type="checkbox" name="is_spicy" <?=checked(isset($_POST['name_tr'])?isset($_POST['is_spicy']):$row['is_spicy'])?>> Acılı</label>
</div>
<div class="full"><button class="btn primary">Kaydet</button></div>
</form></div>
<script>
function addVariant(){
 const d=document.createElement('div');
 d.className='variant-row';
 d.innerHTML='<input class="input" style="margin:0" name="variant_label_tr[]" placeholder="TR: 2 Kişi"><input class="input" style="margin:0" name="variant_label_en[]" placeholder="EN: 2 Persons"><input class="input" style="margin:0" name="variant_price[]" placeholder="Fiyat">';
 document.querySelector('#variants').appendChild(d);
}

// Drag & Drop Upload
(function(){
  var zone = document.getElementById('uploadZone');
  var input = document.getElementById('imageInput');
  var fileName = document.getElementById('fileName');
  var previewWrap = document.getElementById('previewWrap');
  var previewImg = document.getElementById('previewImg');
  var previewName = document.getElementById('previewName');
  var previewMeta = document.getElementById('previewMeta');

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
    if(input.files && input.files[0]){
      showPreview(input.files[0]);
    }
  });

  window.clearUpload = function(){
    input.value = '';
    zone.classList.remove('has-file');
    fileName.textContent = '';
    previewWrap.style.display = 'none';
  };

  window.toggleExistingPreview = function(cb){
    var wrap = document.getElementById('existingWrap');
    if(wrap) wrap.style.opacity = cb.checked ? '0.4' : '1';
  };

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

    var size = file.size < 1024 * 1024
      ? (file.size / 1024).toFixed(0) + ' KB'
      : (file.size / 1024 / 1024).toFixed(1) + ' MB';
    previewMeta.textContent = size + ' · ' + file.type.split('/')[1].toUpperCase();

    var reader = new FileReader();
    reader.onload = function(e){
      previewImg.src = e.target.result;
      previewWrap.style.display = 'flex';
    };
    reader.readAsDataURL(file);
  }
})();
</script>
<?php include __DIR__.'/_bottom.php'; ?>
