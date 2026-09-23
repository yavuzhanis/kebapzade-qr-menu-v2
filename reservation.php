<?php
declare(strict_types=1);
require __DIR__ . '/app/bootstrap.php';

if (!is_post()) redirect('/#reservation');
verify_csrf();

$L = ($_POST['language'] ?? 'tr') === 'en' ? 'en' : 'tr';
if (setting($pdo, 'reservation_enabled', '1') !== '1') {
    flash('reservation_err', $L === 'en' ? 'Online reservations are temporarily unavailable.' : 'Online rezervasyon geçici olarak kapalıdır.');
    redirect('/?lang=' . $L . '#reservation');
}

// Honeypot: bots tend to fill this hidden field.
if (trim((string)($_POST['website'] ?? '')) !== '') redirect('/?lang=' . $L . '#reservation');

$name = trim((string)($_POST['guest_name'] ?? ''));
$phone = trim((string)($_POST['phone'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$date = trim((string)($_POST['reservation_date'] ?? ''));
$time = trim((string)($_POST['reservation_time'] ?? ''));
$guests = (int)($_POST['guest_count'] ?? 2);
$note = trim((string)($_POST['note'] ?? ''));

$errors = [];
if (mb_strlen($name) < 2 || mb_strlen($name) > 160) $errors[] = $L === 'en' ? 'Please enter your name.' : 'Ad soyad alanını kontrol edin.';
if (mb_strlen(preg_replace('/\D+/', '', $phone)) < 10) $errors[] = $L === 'en' ? 'Please enter a valid phone number.' : 'Geçerli bir telefon numarası girin.';
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = $L === 'en' ? 'Please check your email address.' : 'E-posta adresini kontrol edin.';
if ($guests < 1 || $guests > 30) $errors[] = $L === 'en' ? 'Guest count must be between 1 and 30.' : 'Kişi sayısı 1 ile 30 arasında olmalıdır.';

$dt = DateTime::createFromFormat('Y-m-d H:i', $date . ' ' . $time);
$dtErrors = DateTime::getLastErrors();
if (!$dt || ($dtErrors !== false && ($dtErrors['warning_count'] || $dtErrors['error_count']))) {
    $errors[] = $L === 'en' ? 'Please select a valid date and time.' : 'Geçerli tarih ve saat seçin.';
} else {
    $now = new DateTime('now');
    $max = (clone $now)->modify('+120 days');
    if ($dt < $now || $dt > $max) $errors[] = $L === 'en' ? 'Reservation date is outside the available range.' : 'Rezervasyon tarihi uygun aralıkta değil.';
    $hm = (int)$dt->format('Hi');
    if ($hm < 1000 || $hm > 2230) $errors[] = $L === 'en' ? 'Online requests are accepted between 10:00 and 22:30.' : 'Online talepler 10.00–22.30 saatleri için alınmaktadır.';
}

if ($errors) {
    $_SESSION['reservation_old'] = $_POST;
    flash('reservation_err', implode(' ', $errors));
    redirect('/?lang=' . $L . '#reservation');
}

try {
    // Prevent accidental double-submit of the exact same reservation within 2 minutes.
    $dup = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE phone=? AND reservation_date=? AND reservation_time=? AND created_at >= (NOW() - INTERVAL 2 MINUTE)");
    $dup->execute([$phone, $date, $time]);
    if ((int)$dup->fetchColumn() === 0) {
        $q = $pdo->prepare('INSERT INTO reservations(guest_name,phone,email,reservation_date,reservation_time,guest_count,note,status,source,language) VALUES(?,?,?,?,?,?,?,\'pending\',\'website\',?)');
        $q->execute([$name, $phone, $email ?: null, $date, $time, $guests, mb_substr($note, 0, 2000), $L]);
    }
    unset($_SESSION['reservation_old']);
    flash('reservation_ok', $L === 'en' ? 'We received your reservation request. Our team will contact you for confirmation.' : 'Rezervasyon talebinizi aldık. Kesinleştirmek için ekibimiz sizinle iletişime geçecektir.');
} catch (Throwable $e) {
    $_SESSION['reservation_old'] = $_POST;
    flash('reservation_err', $L === 'en' ? 'Online reservation is being updated. Please contact us by phone.' : 'Online rezervasyon sistemi güncelleniyor. Lütfen telefonla iletişime geçin.');
}
redirect('/?lang=' . $L . '#reservation');
