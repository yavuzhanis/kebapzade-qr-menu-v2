<?php
require __DIR__ . '/../app/bootstrap.php'; require_admin();
$id=(int)($_GET['id']??0);$row=['id'=>0,'name'=>'','email'=>''];
if($id){$q=$pdo->prepare('SELECT id,name,email FROM admins WHERE id=?');$q->execute([$id]);$row=$q->fetch()?:$row;if(!$row['id']) redirect('/admin/users.php');}
$error='';
if(is_post()){
 verify_csrf();$name=trim((string)($_POST['name']??''));$email=mb_strtolower(trim((string)($_POST['email']??'')));$password=(string)($_POST['password']??'');
 if($name===''||!filter_var($email,FILTER_VALIDATE_EMAIL)){$error='Ad ve geçerli e-posta zorunludur.';}
 elseif(!$id&&strlen($password)<10){$error='Yeni yönetici için şifre en az 10 karakter olmalıdır.';}
 elseif($id&&$password!==''&&strlen($password)<10){$error='Yeni şifre en az 10 karakter olmalıdır.';}
 else{
  try{
   if($id){
    if($password!==''){$q=$pdo->prepare('UPDATE admins SET name=?,email=?,password_hash=? WHERE id=?');$q->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT),$id]);}
    else{$q=$pdo->prepare('UPDATE admins SET name=?,email=? WHERE id=?');$q->execute([$name,$email,$id]);}
    if($id===(int)(admin_user()['id']??0)){$_SESSION['admin_user']['name']=$name;$_SESSION['admin_user']['email']=$email;}
   }else{$q=$pdo->prepare('INSERT INTO admins(name,email,password_hash) VALUES(?,?,?)');$q->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT)]);}
   flash('ok','Yönetici hesabı kaydedildi.');redirect('/admin/users.php');
  }catch(Throwable $e){$error='Bu e-posta başka bir yönetici tarafından kullanılıyor olabilir.';}
 }
}
$pageTitle=$id?'Yönetici Düzenle':'Yönetici Ekle';$active='users';include __DIR__.'/_top.php';
?>
<div class="head"><div><h1><?=e($pageTitle)?></h1><p>Panel erişimi için ayrı kullanıcı hesabı oluşturun.</p></div><a class="btn" href="<?=e(base_url('/admin/users.php'))?>">← Geri</a></div>
<?php if($error):?><div class="notice err"><?=e($error)?></div><?php endif;?>
<div class="panel"><form class="form grid" method="post"><?=csrf_field()?>
<label>Ad Soyad<input class="input" name="name" required maxlength="120" value="<?=e($_POST['name']??$row['name'])?>"></label>
<label>E-posta<input class="input" type="email" name="email" required maxlength="190" value="<?=e($_POST['email']??$row['email'])?>"></label>
<label class="full"><?=$id?'Yeni Şifre (değişmeyecekse boş bırakın)':'Şifre'?><input class="input" type="password" name="password" <?=$id?'':'required'?> minlength="10" autocomplete="new-password"></label>
<div class="full"><button class="btn primary">Kaydet</button></div></form></div>
<?php include __DIR__.'/_bottom.php'; ?>
