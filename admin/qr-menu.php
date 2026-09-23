<?php
require __DIR__ . '/../app/bootstrap.php';
require_admin();

$targetType = (string)($_GET['target'] ?? 'menu');
$tableNum = trim((string)($_GET['table'] ?? ''));

$baseUrl = base_url('/');
if (str_starts_with($baseUrl, '/')) {
    $scheme = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) ? 'https' : 'http';
    $baseUrl = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $baseUrl;
}

if ($targetType === 'welcome') {
    $url = rtrim($baseUrl, '/') . '/';
} else {
    $url = rtrim($baseUrl, '/') . '/qr-menu.php';
    if ($tableNum !== '') {
        $url .= '?table=' . urlencode($tableNum);
    }
}

$pageTitle = 'Masa QR Kod & Baskı';
$active = 'qr-menu';
include __DIR__ . '/_top.php';
?>

<div class="head">
    <div>
        <h1>Masa QR Kod &amp; Baskı</h1>
        <p>Müşterilerin masadan telefonlarıyla menüyü görüntülemesi için QR kod oluşturun ve yazdırın.</p>
    </div>
</div>

<div class="grid" style="grid-template-columns: 360px 1fr; gap: 24px; align-items: start;">
    <!-- Settings Panel -->
    <div class="panel">
        <div class="panel-title">QR Kod Seçenekleri</div>
        <form class="form" method="get" action="">
            <label>Hedef Sayfa
                <select name="target" class="input" onchange="this.form.submit()">
                    <option value="menu" <?= $targetType === 'menu' ? 'selected' : '' ?>>Doğrudan Dijital Menü (/qr-menu.php)</option>
                    <option value="welcome" <?= $targetType === 'welcome' ? 'selected' : '' ?>>Karşılama Ekranı (/)</option>
                </select>
            </label>

            <?php if ($targetType === 'menu'): ?>
                <label style="margin-top: 14px;">Masa Numarası (İsteğe Bağlı)
                    <input type="text" name="table" class="input" placeholder="Örn: 1, 2, Bahçe 4" value="<?= e($tableNum) ?>">
                    <small class="muted" style="display:block;margin-top:4px;">Masa numarası girerseniz menüde "Masa: X" olarak görünür.</small>
                </label>
            <?php endif; ?>

            <div style="margin-top: 18px; display: flex; gap: 10px;">
                <button type="submit" class="btn">Kodu Güncelle</button>
                <button type="button" class="btn primary" onclick="window.print()">🖨️ Yazdır / PDF</button>
            </div>
        </form>
    </div>

    <!-- QR Print Preview Card -->
    <div class="panel">
        <div class="panel-title">Baskı Önizleme (Masa Kartı)</div>
        <div class="form" style="display: flex; flex-direction: column; align-items: center; padding: 36px 20px;">

            <!-- Printable Stand Card Frame -->
            <div id="printCard" style="width: 290px; padding: 30px 24px 26px; border: 2px solid #d4af37; border-radius: 20px; background: #ffffff; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.06); color: #1e130f;">
                <div style="font-family: 'Cinzel', serif; font-size: 19px; font-weight: 800; color: #966e1d; letter-spacing: 0.1em; margin-bottom: 2px;">KEBAPZADE</div>
                <div style="font-size: 9.5px; font-weight: 700; letter-spacing: 0.2em; color: #5a4b43; text-transform: uppercase; margin-bottom: 16px;">KAPADOKYA · GÖREME</div>

                <?php if ($tableNum !== ''): ?>
                    <div style="display: inline-block; background: #2c0e09; color: #fdf3d8; padding: 4px 14px; border-radius: 999px; font-size: 12px; font-weight: 800; margin-bottom: 14px; letter-spacing: 0.04em;">
                        MASA <?= e($tableNum) ?>
                    </div>
                <?php endif; ?>

                <!-- QR Code Canvas Container -->
                <div id="qrcode" style="width: 200px; height: 200px; margin: 0 auto 16px; background: #fff; display: flex; align-items: center; justify-content: center;"></div>

                <p style="margin: 0 0 4px; font-size: 13px; font-weight: 800; color: #1e130f;">KAMERANIZLA OKUTUN</p>
                <p style="margin: 0; font-size: 11px; color: #726257;">Fotoğraflı Dijital QR Menü</p>
            </div>

            <div style="margin-top: 18px; text-align: center;">
                <p class="muted" style="font-size: 12px; margin-bottom: 4px;">Karekodun yönlendirdiği tam bağlantı:</p>
                <code style="font-size: 12px; background: var(--panel-subtle); padding: 4px 10px; border-radius: 6px; border: 1px solid var(--line); word-break: break-all;"><?= e($url) ?></code>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    new QRCode(document.getElementById('qrcode'), {
        text: <?= json_encode($url, JSON_UNESCAPED_SLASHES) ?>,
        width: 200,
        height: 200,
        colorDark: "#1a0805",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
</script>

<?php include __DIR__ . '/_bottom.php'; ?>