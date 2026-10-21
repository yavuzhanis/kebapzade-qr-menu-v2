<?php
require __DIR__ . '/../app/bootstrap.php'; require_admin(); require_once __DIR__.'/../app/upload.php';
$id=(int)($_GET['id'] ?? 0);
$row=['id'=>0,'name_tr'=>'','name_en'=>'','slug'=>'','description_tr'=>'','description_en'=>'','image_path'=>'','sort_order'=>0,'is_active'=>1];
if($id){$q=$pdo->prepare('SELECT * FROM categories WHERE id=?');$q->execute([$id]);$row=$q->fetch() ?: $row;}
$error='';
if(is_post()){
 verify_csrf();
 $nameTr=trim($_POST['name_tr']??'');$nameEn=trim($_POST['name_en']??'');$slug=slugify($_POST['slug']??$nameTr);
 if($nameTr===''){$error='Türkçe kategori adı zorunludur.';}
 else{
  try{
   $img=$row['image_path']??'';$newImg=upload_menu_image($_FILES['image']??[],$config);
   if($newImg){delete_uploaded_image($img);$img=$newImg;}
   if(isset($_POST['remove_image'])&&$img){delete_uploaded_image($img);$img=null;}
   if($id){
    $q=$pdo->prepare('UPDATE categories SET name_tr=?,name_en=?,slug=?,description_tr=?,description_en=?,image_path=?,sort_order=?,is_active=? WHERE id=?');
    $q->execute([$nameTr,$nameEn,$slug,trim($_POST['description_tr']??''),trim($_POST['description_en']??''),$img,(int)($_POST['sort_order']??0),isset($_POST['is_active'])?1:0,$id]);
   }else{
    $q=$pdo->prepare('INSERT INTO categories(name_tr,name_en,slug,description_tr,description_en,image_path,sort_order,is_active) VALUES(?,?,?,?,?,?,?,?)');
    $q->execute([$nameTr,$nameEn,$slug,trim($_POST['description_tr']??''),trim($_POST['description_en']??''),$img,(int)($_POST['sort_order']??0),isset($_POST['is_active'])?1:0]);
   }
   clear_menu_cache();
   flash('ok','Kategori kaydedildi.');redirect('/admin/categories.php');
  }catch(Throwable $e){$error='Kategori kaydedilemedi. Slug benzersiz olmalıdır.';}
 }
}
$pageTitle=$id?'Kategori Düzenle':'Kategori Ekle';$active='categories';include __DIR__.'/_top.php';
?>
<div class="head"><div><h1><?=e($pageTitle)?></h1><p>Türkçe/İngilizce isim, slug ve sıra bilgisi.</p></div><a class="btn" href="<?=e(base_url('/admin/categories.php'))?>">← Geri</a></div>
<?php if($error):?><div class="notice err"><?=e($error)?></div><?php endif;?>
<div class="panel"><form class="form grid" method="post" enctype="multipart/form-data"><?=csrf_field()?>
<label>Türkçe Ad<input class="input" name="name_tr" required value="<?=e($_POST['name_tr']??$row['name_tr'])?>"></label>
<label>İngilizce Ad<input class="input" name="name_en" value="<?=e($_POST['name_en']??$row['name_en'])?>"></label>
<label>Slug<input class="input" name="slug" value="<?=e($_POST['slug']??$row['slug'])?>" placeholder="otomatik-olusturulur"></label>
<label>Sıra<input class="input" type="number" name="sort_order" value="<?=e((string)($_POST['sort_order']??$row['sort_order']))?>"></label>
<label class="full">Türkçe Açıklama<textarea name="description_tr"><?=e($_POST['description_tr']??$row['description_tr'])?></textarea></label>
<label class="full">İngilizce Açıklama<textarea name="description_en"><?=e($_POST['description_en']??$row['description_en'])?></textarea></label>

<!-- Gelişmiş Görsel Yükleme Alanı -->
<div class="full">
  <label style="margin-bottom:4px">QR Menü Kategori Kapak Görseli</label>
  <div class="upload-zone" id="uploadZone">
    <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" id="imageInput">
    <div class="upload-zone-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
    </div>
    <div class="upload-zone-text">Kapak görselini buraya sürükleyin veya tıklayın</div>
    <div class="upload-zone-hint">JPG, PNG veya WEBP · Maks. 4 MB · Yatay en az 1200 × 650 px önerilir</div>
    <div class="upload-zone-filename" id="fileName"></div>
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

<?php if(!empty($row['image_path'])):?>
<div class="full">
  <div class="existing-image-wrap" id="existingWrap">
    <img src="<?=e(image_url($row['image_path']))?>" alt="Mevcut kapak görseli">
    <div class="existing-image-info">
      <span class="label-tag">Mevcut Kapak Görseli</span>
      <p>Bu görsel QR menüde kategori kapak fotoğrafı olarak görünüyor. Kaldırmak için alttaki kutuyu işaretleyin veya yeni bir görsel yükleyin.</p>
      <div class="checks">
        <label><input type="checkbox" name="remove_image" onchange="toggleExistingPreview(this)"> Mevcut kapak görselini kaldır</label>
      </div>
    </div>
  </div>
</div>
<?php endif;?>

<div class="checks full"><label><input type="checkbox" name="is_active" <?=checked(isset($_POST['name_tr'])?isset($_POST['is_active']):$row['is_active'])?>> Yayında</label></div>
<div class="full"><button class="btn primary">Kaydet</button></div>
</form></div>

<script>
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
