<?php
require __DIR__ . '/../app/bootstrap.php'; require_admin();
$cat=(int)($_GET['category']??0);$qstr=trim($_GET['q']??'');
$sql='SELECT i.*,c.name_tr category_name FROM items i JOIN categories c ON c.id=i.category_id WHERE 1=1';
$args=[];
if($cat){$sql.=' AND i.category_id=?';$args[]=$cat;}
if($qstr!==''){$sql.=' AND (i.name_tr LIKE ? OR i.name_en LIKE ?)';$args[]='%'.$qstr.'%';$args[]='%'.$qstr.'%';}
$sql.=' ORDER BY c.sort_order,i.sort_order,i.id';
$q=$pdo->prepare($sql);$q->execute($args);$rows=$q->fetchAll();
$cats=$pdo->query('SELECT id,name_tr FROM categories ORDER BY sort_order,id')->fetchAll();
$pageTitle='Ürünler';$active='items';include __DIR__.'/_top.php';
?>
<div class="head"><div><h1>Ürünler</h1><p>Menü ürünlerini, fiyatları ve görünürlüğü yönetin.</p></div><a class="btn primary" href="<?=e(base_url('/admin/item-form.php'))?>">+ Ürün Ekle</a></div>
<div class="panel"><form class="form filter-bar" method="get">
<input class="input" name="q" value="<?=e($qstr)?>" placeholder="Ürün ara...">
<select class="input" name="category"><option value="">Tüm kategoriler</option><?php foreach($cats as $c):?><option value="<?=$c['id']?>" <?=selected($cat,$c['id'])?>><?=e($c['name_tr'])?></option><?php endforeach;?></select>
<button class="btn">Filtrele</button><a class="btn" href="<?=e(base_url('/admin/items.php'))?>">Temizle</a>
</form></div>
<div class="panel"><div class="table-wrap"><table>
<thead><tr><th>Görsel</th><th>Ürün</th><th>Kategori</th><th>Fiyat</th><th>Sıra</th><th>Durum</th><th></th></tr></thead><tbody>
<?php foreach($rows as $r):?><tr>
<td><?php if($r['image_path']):?><img class="thumb" src="<?=e(image_url($r['image_path']))?>" alt=""><?php else:?><span class="muted">—</span><?php endif;?></td>
<td><strong><?=e($r['name_tr'])?></strong><div class="muted"><?=e($r['name_en'])?></div></td>
<td><?=e($r['category_name'])?></td><td><?=e(money($r['price']))?></td><td><?=$r['sort_order']?></td>
<td><span class="status <?=$r['is_active']?'on':'off'?>"><?=$r['is_active']?'Aktif':'Pasif'?></span></td>
<td><div class="actions"><a class="btn sm" href="<?=e(base_url('/admin/item-form.php?id='.$r['id']))?>">Düzenle</a>
<form method="post" action="<?=e(base_url('/admin/item-delete.php'))?>" onsubmit="return confirm('Bu ürün silinsin mi?')"><?=csrf_field()?><input type="hidden" name="id" value="<?=$r['id']?>"><button class="btn sm danger">Sil</button></form></div></td>
</tr><?php endforeach;?></tbody></table></div></div>
<?php include __DIR__.'/_bottom.php'; ?>
