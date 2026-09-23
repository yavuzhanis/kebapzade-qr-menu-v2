<?php
require __DIR__ . '/app/bootstrap.php';
$L = lang();
header('Location: ' . base_url('/qr-menu.php?lang=' . $L));
exit;
