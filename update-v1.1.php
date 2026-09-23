<?php
declare(strict_types=1);
require __DIR__ . '/app/bootstrap.php';
require_admin();

$error = '';
$done = false;
if (is_post()) {
    verify_csrf();
    try {
        $sql = file_get_contents(__DIR__ . '/database/upgrade_v1_1.sql');
        $pdo->exec($sql);
        $defaults = [
            'reservation_enabled' => '1',
            'reservation_notice_tr' => 'Rezervasyon talebiniz ekibimiz tarafından kontrol edilerek telefonla kesinleştirilir.',
            'reservation_notice_en' => 'Your reservation request is confirmed by our team by phone.',
            'service_outdoor' => '1',
            'service_fireplace' => '1',
            'service_private_room' => '1',
            'price_range' => '₺₺',
            'hero_image' => '',
            'story_image' => '',
        ];
        $q = $pdo->prepare('INSERT INTO settings(`key`,`value`) VALUES(?,?) ON DUPLICATE KEY UPDATE `key`=`key`');
        foreach ($defaults as $k => $v) $q->execute([$k, $v]);
        $done = true;
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}
?>
<!doctype html><html lang="tr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Kebapzade V1.1 Güncelleme</title><link rel="stylesheet" href="<?=e(base_url('/assets/css/admin.css'))?>"></head>
<body class="login-page"><div class="login"><h1>V1.1 Güncelleme</h1>
<p>Rezervasyon tablosunu ve yeni site ayarlarını mevcut menü verilerinizi silmeden ekler.</p>
<?php if ($error): ?><div class="notice err"><?=e($error)?></div><?php endif; ?>
<?php if ($done): ?><div class="notice ok"><strong>Güncelleme tamamlandı.</strong><br>Bu dosyayı sunucudan silebilirsiniz.</div><a class="btn primary" href="<?=e(base_url('/admin/index.php'))?>">Panele Dön</a>
<?php else: ?><form method="post"><?=csrf_field()?><button class="btn primary" style="width:100%">V1.1'e Güncelle</button></form><?php endif; ?>
</div></body></html>
