<?php
require __DIR__ . '/../app/bootstrap.php'; require_admin();
$id=(int)($_GET['id']??$_POST['id']??0);
if(!$id) redirect('/admin/reservations.php');
$labels=['pending'=>'Bekliyor','confirmed'=>'Onaylandı','cancelled'=>'İptal','completed'=>'Tamamlandı'];
if(is_post()){
 verify_csrf();
 $status=(string)($_POST['status']??'pending');
 if(!array_key_exists($status,$labels)) $status='pending';
 $note=mb_substr(trim((string)($_POST['admin_note']??'')),0,3000);
 $q=$pdo->prepare('UPDATE reservations SET status=?,admin_note=? WHERE id=?');$q->execute([$status,$note,$id]);
 flash('ok','Rezervasyon güncellendi.');redirect('/admin/reservation.php?id='.$id);
}
$q=$pdo->prepare('SELECT * FROM reservations WHERE id=?');$q->execute([$id]);$r=$q->fetch();if(!$r) redirect('/admin/reservations.php');
$pageTitle='Rezervasyon #'.$id;$active='reservations';include __DIR__.'/_top.php';
?>
<div class="head"><div><h1>Rezervasyon #<?=$id?></h1><p><?=e(date('d.m.Y',strtotime($r['reservation_date'])))?> · <?=e(substr($r['reservation_time'],0,5))?> · <?=$r['guest_count']?> kişi</p></div><a class="btn" href="<?=e(base_url('/admin/reservations.php'))?>">← Liste</a></div>
<div class="panel"><div class="form grid">
<div><span class="muted">Misafir</span><h3><?=e($r['guest_name'])?></h3></div>
<div><span class="muted">Telefon</span><h3><a href="tel:<?=e(preg_replace('/[^\d+]/','',$r['phone']))?>"><?=e($r['phone'])?></a></h3></div>
<div><span class="muted">E-posta</span><p><?=e($r['email']?:'—')?></p></div>
<div><span class="muted">Kaynak</span><p><?=e($r['source'])?> / <?=e(strtoupper($r['language']))?></p></div>
<div class="full"><span class="muted">Misafir Notu</span><p style="white-space:pre-wrap"><?=e($r['note']?:'—')?></p></div>
</div></div>
<div class="panel"><div class="panel-title">Durum Güncelle</div><form class="form grid" method="post"><?=csrf_field()?><input type="hidden" name="id" value="<?=$id?>">
<label>Durum<select name="status"><?php foreach($labels as $k=>$v):?><option value="<?=e($k)?>" <?=selected($r['status'],$k)?>><?=e($v)?></option><?php endforeach;?></select></label>
<label class="full">Yönetim Notu<textarea name="admin_note"><?=e($r['admin_note'])?></textarea></label>
<div class="full"><button class="btn primary">Kaydet</button></div>
</form></div>
<?php include __DIR__.'/_bottom.php'; ?>
