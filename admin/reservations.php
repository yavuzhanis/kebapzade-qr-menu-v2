<?php
require __DIR__ . '/../app/bootstrap.php'; require_admin();
$status = trim((string)($_GET['status'] ?? ''));
$date = trim((string)($_GET['date'] ?? ''));
$sql = 'SELECT * FROM reservations WHERE 1=1'; $args=[];
if (in_array($status, ['pending','confirmed','cancelled','completed'], true)) { $sql .= ' AND status=?'; $args[]=$status; }
if ($date !== '') { $sql .= ' AND reservation_date=?'; $args[]=$date; }
$sql .= ' ORDER BY reservation_date ASC, reservation_time ASC, id DESC';
$error=''; $rows=[];
try { $q=$pdo->prepare($sql); $q->execute($args); $rows=$q->fetchAll(); }
catch(Throwable $e){ $error='Rezervasyon tablosu henüz kurulmamış. V1.1 güncellemesini çalıştırın.'; }
$labels=['pending'=>'Bekliyor','confirmed'=>'Onaylandı','cancelled'=>'İptal','completed'=>'Tamamlandı'];
$pageTitle='Rezervasyonlar';$active='reservations';include __DIR__.'/_top.php';
?>
<div class="head"><div><h1>Rezervasyonlar</h1><p>Web sitesinden gelen masa taleplerini yönetin.</p></div><div style="display:flex;gap:8px"><a class="btn" href="<?=e(base_url('/admin/reservations-export.php'))?>">CSV İndir</a></div></div>
<?php if($error):?><div class="notice err"><?=e($error)?> <a href="<?=e(base_url('/update-v1.1.php'))?>"><u>Güncellemeyi aç</u></a></div><?php endif;?>
<div class="panel"><form class="form" method="get" style="display:flex;gap:10px;flex-wrap:wrap">
<select class="input" style="max-width:220px;margin:0" name="status"><option value="">Tüm durumlar</option><?php foreach($labels as $k=>$v):?><option value="<?=e($k)?>" <?=selected($status,$k)?>><?=e($v)?></option><?php endforeach;?></select>
<input class="input" style="max-width:220px;margin:0" type="date" name="date" value="<?=e($date)?>">
<button class="btn">Filtrele</button><a class="btn" href="<?=e(base_url('/admin/reservations.php'))?>">Temizle</a>
</form></div>
<div class="panel"><div class="table-wrap"><table><thead><tr><th>Tarih</th><th>Misafir</th><th>Kişi</th><th>Telefon</th><th>Durum</th><th>Talep</th><th></th></tr></thead><tbody>
<?php foreach($rows as $r):?><tr>
<td><strong><?=e(date('d.m.Y',strtotime($r['reservation_date'])))?></strong><div class="muted"><?=e(substr($r['reservation_time'],0,5))?></div></td>
<td><strong><?=e($r['guest_name'])?></strong><?php if($r['email']):?><div class="muted"><?=e($r['email'])?></div><?php endif;?></td>
<td><?=$r['guest_count']?></td><td><a href="tel:<?=e(preg_replace('/[^\d+]/','',$r['phone']))?>"><?=e($r['phone'])?></a></td>
<td><span class="status <?=$r['status']==='confirmed'||$r['status']==='completed'?'on':($r['status']==='cancelled'?'off':'')?>"><?=e($labels[$r['status']]??$r['status'])?></span></td>
<td class="muted"><?=e(date('d.m H:i',strtotime($r['created_at'])))?></td>
<td><div class="actions"><a class="btn sm" href="<?=e(base_url('/admin/reservation.php?id='.$r['id']))?>">Aç</a></div></td>
</tr><?php endforeach;?>
<?php if(!$rows&&!$error):?><tr><td colspan="7" class="muted">Kayıt bulunamadı.</td></tr><?php endif;?>
</tbody></table></div></div>
<?php include __DIR__.'/_bottom.php'; ?>
