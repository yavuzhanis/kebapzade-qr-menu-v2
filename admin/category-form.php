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
<label class="full">QR Menü Kategori Kapak Görseli<input class="input" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"><span class="muted">Yatay, en az 1200 × 650 px önerilir.</span></label>
<?php if(!empty($row['image_path'])):?><div class="full"><img class="preview" src="<?=e(image_url($row['image_path']))?>"><div class="checks"><label><input type="checkbox" name="remove_image"> Mevcut kapak görselini kaldır</label></div></div><?php endif;?>
<div class="checks full"><label><input type="checkbox" name="is_active" <?=checked(isset($_POST['name_tr'])?isset($_POST['is_active']):$row['is_active'])?>> Yayında</label></div>
<div class="full"><button class="btn primary">Kaydet</button></div>
</form></div>
<?php include __DIR__.'/_bottom.php'; ?>
