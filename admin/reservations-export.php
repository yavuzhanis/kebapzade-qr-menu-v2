<?php
require __DIR__ . '/../app/bootstrap.php'; require_admin();
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="kebapzade-rezervasyonlar-'.date('Y-m-d').'.csv"');
echo "\xEF\xBB\xBF";
$out=fopen('php://output','w');
fputcsv($out,['ID','Tarih','Saat','Ad Soyad','Telefon','E-posta','Kişi','Durum','Misafir Notu','Yönetim Notu','Oluşturma'],';');
try{
 $rows=$pdo->query('SELECT * FROM reservations ORDER BY reservation_date DESC,reservation_time DESC')->fetchAll();
 foreach($rows as $r) fputcsv($out,[$r['id'],$r['reservation_date'],substr($r['reservation_time'],0,5),$r['guest_name'],$r['phone'],$r['email'],$r['guest_count'],$r['status'],$r['note'],$r['admin_note'],$r['created_at']],';');
}catch(Throwable $e){}
fclose($out);exit;
