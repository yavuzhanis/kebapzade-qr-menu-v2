<?php
require __DIR__ . '/../app/bootstrap.php'; require_admin(); require_once __DIR__.'/../app/upload.php';
if(!is_post()) redirect('/admin/categories.php');
verify_csrf();
$id=(int)($_POST['id']??0);
if($id){$q=$pdo->prepare('SELECT image_path FROM categories WHERE id=?');$q->execute([$id]);$img=$q->fetchColumn();$q=$pdo->prepare('DELETE FROM categories WHERE id=?');$q->execute([$id]);delete_uploaded_image($img?:null);flash('ok','Kategori silindi.');}
redirect('/admin/categories.php');
