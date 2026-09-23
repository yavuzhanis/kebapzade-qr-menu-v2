<?php
require __DIR__ . '/../app/bootstrap.php'; require_admin();
$rows=$pdo->query('SELECT c.*,COUNT(i.id) item_count FROM categories c LEFT JOIN items i ON i.category_id=c.id GROUP BY c.id ORDER BY c.sort_order,c.id')->fetchAll();
$pageTitle='Kategoriler';$active='categories';include __DIR__.'/_top.php';
?>
<div class="head"><div><h1>Kategoriler</h1><p>Menü bölüm başlıklarını ve sıralamayı yönetin.</p></div><a class="btn primary" href="<?=e(base_url('/admin/category-form.php'))?>">+ Kategori Ekle</a></div>
<div class="panel"><div class="table-wrap"><table>
<thead><tr><th>Sıra</th><th>Türkçe</th><th>İngilizce</th><th>Ürün</th><th>Durum</th><th></th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr>
<td><?=$r['sort_order']?></td><td><strong><?=e($r['name_tr'])?></strong><div class="muted"><?=e($r['slug'])?></div></td><td><?=e($r['name_en'])?></td><td><?=$r['item_count']?></td>
<td><span class="status <?=$r['is_active']?'on':'off'?>"><?=$r['is_active']?'Aktif':'Pasif'?></span></td>
<td><div class="actions"><a class="btn sm" href="<?=e(base_url('/admin/category-form.php?id='.$r['id']))?>">Düzenle</a>
<form method="post" action="<?=e(base_url('/admin/category-delete.php'))?>" onsubmit="return confirm('Kategori ve içindeki TÜM ürünler silinecek. Emin misiniz?')"><?=csrf_field()?><input type="hidden" name="id" value="<?=$r['id']?>"><button class="btn sm danger">Sil</button></form></div></td>
</tr><?php endforeach;?></tbody></table></div></div>
<?php include __DIR__.'/_bottom.php'; ?>
