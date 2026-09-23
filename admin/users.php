<?php
require __DIR__ . '/../app/bootstrap.php'; require_admin();
$rows=$pdo->query('SELECT id,name,email,created_at,updated_at FROM admins ORDER BY id')->fetchAll();
$pageTitle='Yöneticiler';$active='users';include __DIR__.'/_top.php';
?>
<div class="head"><div><h1>Yöneticiler</h1><p>Menü ve rezervasyon paneline erişebilen hesaplar.</p></div><a class="btn primary" href="<?=e(base_url('/admin/user-form.php'))?>">+ Yönetici Ekle</a></div>
<div class="panel"><div class="table-wrap"><table><thead><tr><th>Ad</th><th>E-posta</th><th>Oluşturma</th><th></th></tr></thead><tbody>
<?php foreach($rows as $r):?><tr><td><strong><?=e($r['name'])?></strong><?php if((int)$r['id']===(int)(admin_user()['id']??0)):?><div class="muted">Bu hesap</div><?php endif;?></td><td><?=e($r['email'])?></td><td class="muted"><?=e(date('d.m.Y',strtotime($r['created_at'])))?></td><td><div class="actions"><a class="btn sm" href="<?=e(base_url('/admin/user-form.php?id='.$r['id']))?>">Düzenle</a>
<?php if((int)$r['id']!==(int)(admin_user()['id']??0)):?><form method="post" action="<?=e(base_url('/admin/user-delete.php'))?>" onsubmit="return confirm('Bu yönetici silinsin mi?')"><?=csrf_field()?><input type="hidden" name="id" value="<?=$r['id']?>"><button class="btn sm danger">Sil</button></form><?php endif;?></div></td></tr><?php endforeach;?>
</tbody></table></div></div>
<?php include __DIR__.'/_bottom.php'; ?>
