<?php
declare(strict_types=1);

$config = require __DIR__ . '/config/config.php';
date_default_timezone_set($config['app']['timezone'] ?? 'Europe/Istanbul');
require_once __DIR__ . '/app/db.php';

$expectedToken = (string)(getenv('INSTALL_TOKEN') ?: '');
$providedToken = (string)($_POST['_install_token'] ?? $_GET['token'] ?? '');
if ($expectedToken === '' || $providedToken === '' || !hash_equals($expectedToken, $providedToken)) {
    http_response_code(404);
    exit('Not found');
}

$error = '';
$success = false;
function h(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }

try {
    $pdo = db($config);
    $schema = file_get_contents(__DIR__ . '/database/schema.sql');
    if ($schema === false) throw new RuntimeException('Şema dosyası okunamadı.');
    $pdo->exec($schema);

    $adminCount = (int)$pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();
    if ($adminCount > 0) {
        http_response_code(404);
        exit('Not found');
    }
} catch (Throwable $e) {
    $error = ($config['app']['debug'] ?? false) ? $e->getMessage() : 'Veritabanı kuruluma hazır değil.';
}

if ($error === '' && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $name = trim((string)($_POST['name'] ?? ''));
    $email = mb_strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 12) {
        $error = 'Ad, geçerli e-posta ve en az 12 karakterli şifre girin.';
    } else {
        try {
            $pdo->beginTransaction();
            $q = $pdo->prepare('INSERT INTO admins(name,email,password_hash) VALUES(?,?,?)');
            $q->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);

            $categoryCount = (int)$pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
            $itemCount = (int)$pdo->query('SELECT COUNT(*) FROM items')->fetchColumn();
            if ($categoryCount === 0 && $itemCount === 0) {
                $seed = require __DIR__ . '/database/seed.php';
                $catStmt = $pdo->prepare('INSERT INTO categories(slug,name_tr,name_en,sort_order,is_active) VALUES(?,?,?,?,1)');
                $catIds = [];
                foreach ($seed['categories'] as $cat) {
                    [$slug,$tr,$en,$sort] = $cat;
                    $catStmt->execute([$slug,$tr,$en,$sort]);
                    $catIds[$slug] = (int)$pdo->lastInsertId();
                }

                $itemStmt = $pdo->prepare('INSERT INTO items(category_id,name_tr,name_en,description_tr,description_en,is_featured,is_spicy,is_vegetarian,sort_order,is_active) VALUES(?,?,?,?,?,?,?,?,?,1)');
                $sortMap = [];
                foreach ($seed['items'] as $item) {
                    [$slug,$tr,$en,$dtr,$den,$featured,$spicy,$veg] = $item;
                    $sortMap[$slug] = ($sortMap[$slug] ?? 0) + 10;
                    $itemStmt->execute([$catIds[$slug],$tr,$en,$dtr,$den,$featured,$spicy,$veg,$sortMap[$slug]]);
                }
            }

            $defaults = [
                'restaurant_name' => 'Kapadokya Kebapzade Restaurant',
                'tagline_tr' => 'Lezzetli ve kaliteli yemek tesadüf değildir.',
                'tagline_en' => 'Great taste and quality are never a coincidence.',
                'hero_title_tr' => 'Kapadokya’nın Sofrasında Geleneksel Lezzetler',
                'hero_title_en' => 'Traditional Flavours at the Table of Cappadocia',
                'address' => 'Bilal Eroğlu Cd. No:3, Göreme / Nevşehir',
                'phone' => '+90 384 271 30 12',
                'email' => 'info@kebapzade.com',
                'hours_tr' => 'Her gün 10.00 – 23.00',
                'hours_en' => 'Every day 10:00 – 23:00',
                'whatsapp' => '903842713012',
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=Kebapzade+Goreme',
                'announcement_tr' => 'Taş fırın, kömür ateşi ve yöresel reçeteler.',
                'announcement_en' => 'Stone oven, charcoal fire and regional recipes.',
                'reservation_enabled' => '1',
                'service_outdoor' => '1',
                'service_fireplace' => '1',
                'service_private_room' => '1',
                'price_range' => '₺₺',
            ];
            $st = $pdo->prepare('INSERT INTO settings(`key`,`value`) VALUES(?,?) ON DUPLICATE KEY UPDATE `key`=`key`');
            foreach ($defaults as $k=>$v) $st->execute([$k,$v]);

            $pdo->commit();
            $success = true;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $error = ($config['app']['debug'] ?? false) ? $e->getMessage() : 'Kurulum tamamlanamadı.';
        }
    }
}
?>
<!doctype html>
<html lang="tr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Kebapzade Kurulum</title><style>
*{box-sizing:border-box}body{margin:0;min-height:100vh;display:grid;place-items:center;background:#100d0b;color:#eee;font-family:Inter,Arial,sans-serif}.card{width:min(620px,calc(100% - 32px));background:#191512;border:1px solid #3b2c21;border-radius:24px;padding:34px;box-shadow:0 30px 80px #0008}h1{font-size:34px;margin:0 0 10px;color:#f4d6a0}.muted{color:#a99d91;line-height:1.65}.row{display:grid;gap:14px;margin-top:24px}label{font-size:13px;color:#c3b7aa}input{width:100%;padding:14px 16px;background:#0f0c0a;color:#fff;border:1px solid #44352a;border-radius:12px;margin-top:7px}button{border:0;border-radius:12px;background:#c59048;color:#160f08;font-weight:800;padding:15px 18px;cursor:pointer}.alert{padding:14px;border-radius:12px;margin:18px 0}.error{background:#531c1c}.ok{background:#173e2b}a{color:#f4d6a0}
</style></head><body><div class="card"><h1>Kebapzade Premium</h1><p class="muted">Veritabanını hazırlar ve ilk yönetici hesabını oluşturur. Kurulumdan sonra Vercel'den <strong>INSTALL_TOKEN</strong> değişkenini silin.</p>
<?php if ($error): ?><div class="alert error"><?=h($error)?></div><?php endif; ?>
<?php if ($success): ?><div class="alert ok"><strong>Kurulum tamamlandı.</strong><br><br>Şimdi Vercel Environment Variables bölümünden <strong>INSTALL_TOKEN</strong> değerini silin ve yeniden deploy edin.<br><br><a href="admin/login.php">Yönetim paneline git →</a></div>
<?php elseif ($error === '' || ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST'): ?><form method="post"><input type="hidden" name="_install_token" value="<?=h($providedToken)?>"><div class="row"><label>Yönetici adı<input name="name" required maxlength="120"></label><label>E-posta<input type="email" name="email" required maxlength="190"></label><label>Şifre<input type="password" name="password" required minlength="12" placeholder="En az 12 karakter"></label><button>Kurulumu Başlat</button></div></form><?php endif; ?>
</div></body></html>
