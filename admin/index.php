<?php
require __DIR__ . '/../app/bootstrap.php'; require_admin();
$catCount=(int)$pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
$itemCount=(int)$pdo->query('SELECT COUNT(*) FROM items')->fetchColumn();
$activeCount=(int)$pdo->query('SELECT COUNT(*) FROM items WHERE is_active=1')->fetchColumn();
$recent=$pdo->query('SELECT i.*,c.name_tr category_name FROM items i JOIN categories c ON c.id=i.category_id ORDER BY i.updated_at DESC LIMIT 8')->fetchAll();
$pageTitle='Genel Bakış';$active='dashboard';include __DIR__.'/_top.php';
?>
<div class="head"><div><h1>Genel Bakış</h1><p>Menünün mevcut durumu ve ürün istatistikleri.</p></div><a class="btn primary" href="<?=e(base_url('/admin/item-form.php'))?>">+ Yeni Ürün</a></div>
<div class="cards">
 <div class="stat"><b><?=$itemCount?></b><span>Toplam Ürün</span></div>
 <div class="stat"><b><?=$activeCount?></b><span>Yayındaki Ürün</span></div>
 <div class="stat"><b><?=$catCount?></b><span>Kategori</span></div>
 <div class="stat"><b><a href="<?=e(base_url('/admin/qr-menu.php'))?>" style="color:var(--gold-dark);text-decoration:underline">QR Oluştur ↗</a></b><span>Masa Baskı Kodu</span></div>
</div>
<div class="panel"><div class="panel-title">Son Güncellenen Ürünler</div><div class="table-wrap"><table>
<thead><tr><th>Ürün</th><th>Kategori</th><th>Fiyat</th><th>Durum</th><th></th></tr></thead><tbody>
<?php foreach($recent as $r): ?><tr>
<td><strong><?=e($r['name_tr'])?></strong></td><td class="muted"><?=e($r['category_name'])?></td><td><?=e(money($r['price']))?></td>
<td><span class="status <?=$r['is_active']?'on':'off'?>"><?=$r['is_active']?'Aktif':'Pasif'?></span></td>
<td><div class="actions"><a class="btn sm" href="<?=e(base_url('/admin/item-form.php?id='.$r['id']))?>">Düzenle</a></div></td>
</tr><?php endforeach;?></tbody></table></div></div>
<?php include __DIR__.'/_bottom.php'; ?>
