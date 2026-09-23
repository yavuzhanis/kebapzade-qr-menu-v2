<?php
require __DIR__ . '/../app/bootstrap.php'; require_admin();
if(!is_post()) redirect('/admin/users.php');verify_csrf();$id=(int)($_POST['id']??0);$me=(int)(admin_user()['id']??0);
if($id===$me){flash('err','Kendi hesabınızı silemezsiniz.');redirect('/admin/users.php');}
$count=(int)$pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();
if($count<=1){flash('err','Son yönetici hesabı silinemez.');redirect('/admin/users.php');}
$q=$pdo->prepare('DELETE FROM admins WHERE id=?');$q->execute([$id]);flash('ok','Yönetici hesabı silindi.');redirect('/admin/users.php');
