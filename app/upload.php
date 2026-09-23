<?php
declare(strict_types=1);

function upload_menu_image(array $file, array $config): ?string {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Görsel yüklenemedi.');
    }
    if ((int)$file['size'] > (int)$config['upload']['max_bytes']) {
        throw new RuntimeException('Görsel en fazla 5 MB olabilir.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];
    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Sadece JPG, PNG veya WEBP yükleyebilirsiniz.');
    }

    $dir = $config['upload']['menu_dir'];
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        throw new RuntimeException('Upload klasörü oluşturulamadı.');
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    $full = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
    if (!move_uploaded_file($file['tmp_name'], $full)) {
        throw new RuntimeException('Görsel kaydedilemedi.');
    }
    return '/uploads/menu/' . $filename;
}

function delete_uploaded_image(?string $path): void {
    if (!$path || !str_starts_with($path, '/uploads/menu/')) return;
    $full = __DIR__ . '/../uploads/menu/' . basename($path);
    if (is_file($full)) @unlink($full);
}


function upload_site_image(array $file, array $config): ?string {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) throw new RuntimeException('Site görseli yüklenemedi.');
    if ((int)$file['size'] > (int)$config['upload']['max_bytes']) throw new RuntimeException('Görsel en fazla 5 MB olabilir.');

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
    if (!isset($allowed[$mime])) throw new RuntimeException('Sadece JPG, PNG veya WEBP yükleyebilirsiniz.');

    $dir = __DIR__ . '/../uploads/site';
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) throw new RuntimeException('Site upload klasörü oluşturulamadı.');
    $filename = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $filename)) throw new RuntimeException('Görsel kaydedilemedi.');
    return '/uploads/site/' . $filename;
}

function delete_site_image(?string $path): void {
    if (!$path || !str_starts_with($path, '/uploads/site/')) return;
    $full = __DIR__ . '/../uploads/site/' . basename($path);
    if (is_file($full)) @unlink($full);
}
