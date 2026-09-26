<?php
declare(strict_types=1);

function upload_menu_image(array $file, array $config): ?string {
    return upload_image($file, $config, 'menu');
}

function upload_site_image(array $file, array $config): ?string {
    return upload_image($file, $config, 'site');
}

function delete_uploaded_image(?string $path): void {
    global $config;
    delete_stored_image($path, $config, 'menu');
}

function delete_site_image(?string $path): void {
    global $config;
    delete_stored_image($path, $config, 'site');
}

function upload_image(array $file, array $config, string $scope): ?string {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Görsel yüklenemedi.');
    }

    $maxBytes = (int)($config['upload']['max_bytes'] ?? 4 * 1024 * 1024);
    if ((int)($file['size'] ?? 0) > $maxBytes) {
        $maxMb = number_format($maxBytes / 1024 / 1024, 1, ',', '');
        throw new RuntimeException("Görsel en fazla {$maxMb} MB olabilir.");
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = (string)$finfo->file((string)$file['tmp_name']);
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];
    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Sadece JPG, PNG veya WEBP yükleyebilirsiniz.');
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    $driver = strtolower((string)($config['storage']['driver'] ?? 'local'));

    if ($driver === 'vercel_blob') {
        return vercel_blob_put("{$scope}/{$filename}", (string)$file['tmp_name'], $mime, $config);
    }

    if ($driver !== 'local') {
        throw new RuntimeException('Desteklenmeyen storage driver: ' . $driver);
    }

    $dir = $scope === 'site'
        ? (string)($config['upload']['site_dir'] ?? __DIR__ . '/../uploads/site')
        : (string)($config['upload']['menu_dir'] ?? __DIR__ . '/../uploads/menu');

    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        throw new RuntimeException('Upload klasörü oluşturulamadı.');
    }

    $full = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
    if (!move_uploaded_file((string)$file['tmp_name'], $full)) {
        throw new RuntimeException('Görsel kaydedilemedi.');
    }

    return '/uploads/' . $scope . '/' . $filename;
}

function delete_stored_image(?string $path, array $config, string $scope): void {
    if (!$path) return;

    if (preg_match('~^https://[^/]+\.blob\.vercel-storage\.com/~i', $path)) {
        vercel_blob_delete($path, $config);
        return;
    }

    $prefix = '/uploads/' . $scope . '/';
    if (!str_starts_with($path, $prefix)) return;

    $dir = $scope === 'site'
        ? (string)($config['upload']['site_dir'] ?? __DIR__ . '/../uploads/site')
        : (string)($config['upload']['menu_dir'] ?? __DIR__ . '/../uploads/menu');
    $full = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . basename($path);
    if (is_file($full)) @unlink($full);
}

function vercel_blob_put(string $pathname, string $tmpFile, string $mime, array $config): string {
    if (!extension_loaded('curl')) {
        throw new RuntimeException('Vercel Blob için PHP cURL eklentisi gerekli.');
    }

    [$headers, $apiUrl] = vercel_blob_auth($config);
    $url = $apiUrl . '/?pathname=' . rawurlencode($pathname);
    $body = file_get_contents($tmpFile);
    if ($body === false) throw new RuntimeException('Yüklenen dosya okunamadı.');

    $headers[] = 'Content-Type: application/octet-stream';
    $headers[] = 'x-content-type: ' . $mime;
    $headers[] = 'x-add-random-suffix: 0';
    $headers[] = 'x-allow-overwrite: 0';
    $headers[] = 'x-api-version: 12';
    $headers[] = 'x-api-blob-request-attempt: 0';
    $headers[] = 'x-api-blob-request-id: php:' . time() . ':' . bin2hex(random_bytes(8));

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 60,
    ]);
    $response = curl_exec($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false || $curlError !== '') {
        throw new RuntimeException('Vercel Blob bağlantı hatası: ' . $curlError);
    }

    $data = json_decode((string)$response, true);
    if ($status < 200 || $status >= 300 || !is_array($data) || empty($data['url'])) {
        $message = is_array($data) ? (string)($data['error']['message'] ?? $data['message'] ?? '') : '';
        throw new RuntimeException('Vercel Blob yükleme hatası' . ($message !== '' ? ': ' . $message : '.'));
    }

    return (string)$data['url'];
}

function vercel_blob_delete(string $urlOrPathname, array $config): void {
    if (!extension_loaded('curl')) return;

    try {
        [$headers, $apiUrl] = vercel_blob_auth($config);
    } catch (Throwable) {
        return;
    }

    $headers[] = 'Content-Type: application/json';
    $headers[] = 'x-api-version: 12';
    $headers[] = 'x-api-blob-request-attempt: 0';
    $headers[] = 'x-api-blob-request-id: php:' . time() . ':' . bin2hex(random_bytes(8));

    $ch = curl_init($apiUrl . '/delete');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode(['urls' => [$urlOrPathname]], JSON_UNESCAPED_SLASHES),
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 30,
    ]);
    curl_exec($ch);
    curl_close($ch);
}

function vercel_blob_auth(array $config): array {
    $storage = $config['storage'] ?? [];
    $token = trim((string)($storage['blob_token'] ?? ''));
    $oidcToken = trim((string)($storage['blob_oidc_token'] ?? ''));
    $storeId = trim((string)($storage['blob_store_id'] ?? ''));
    $apiUrl = rtrim((string)($storage['blob_api_url'] ?? 'https://vercel.com/api/blob'), '/');

    if ($token !== '') {
        return [['Authorization: Bearer ' . $token], $apiUrl];
    }

    if ($oidcToken !== '' && $storeId !== '') {
        return [[
            'Authorization: Bearer ' . $oidcToken,
            'x-vercel-blob-store-id: ' . $storeId,
        ], $apiUrl];
    }

    throw new RuntimeException('Vercel Blob bağlı değil. BLOB_READ_WRITE_TOKEN veya VERCEL_OIDC_TOKEN + BLOB_STORE_ID gerekli.');
}
