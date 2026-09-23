<?php
declare(strict_types=1);
require __DIR__ . '/app/bootstrap.php';
require_admin();
$error = '';
$done = false;
if (is_post()) {
    verify_csrf();
    try {
        $dbName = (string)$config['db']['name'];
        $q = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME='categories' AND COLUMN_NAME='image_path'");
        $q->execute([$dbName]);
        if (!(int)$q->fetchColumn()) $pdo->exec('ALTER TABLE categories ADD COLUMN image_path VARCHAR(255) NULL AFTER description_en');
        $st = $pdo->prepare('INSERT INTO settings(`key`,`value`) VALUES(?,?) ON DUPLICATE KEY UPDATE `key`=`key`');
        $st->execute(['logo_image','']);
        $done = true;
    } catch (Throwable $e) { $error = $e->getMessage(); }
}
?>
<!doctype html><html lang="tr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Kebapzade QR Menü V2 Güncelleme</title><link rel="stylesheet" href="<?=e(base_url('/assets/css/admin.css'))?>"></head>
<body class="login-page"><div class="login"><h1>QR Menü V2 Güncelleme</h1><p>Kategori kapak görseli ve logo alanını mevcut menü verilerinizi silmeden ekler.</p>
<?php if ($error): ?><div class="notice err"><?=e($error)?></div><?php endif; ?>
<?php if ($done): ?><div class="notice ok"><strong>Güncelleme tamamlandı.</strong><br>Güvenlik için bu dosyayı sunucudan silebilirsiniz.</div><a class="btn primary" href="<?=e(base_url('/admin/categories.php'))?>">Kategori Görsellerine Git</a>
<?php else: ?><form method="post"><?=csrf_field()?><button class="btn primary" style="width:100%">V2'ye Güncelle</button></form><?php endif; ?></div></body></html>
