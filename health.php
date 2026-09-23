<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
$out=['php'=>PHP_VERSION,'pdo_mysql'=>extension_loaded('pdo_mysql'),'fileinfo'=>extension_loaded('fileinfo')];
echo json_encode($out, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
