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

    $uploadError = (int)($file['error'] ?? UPLOAD_ERR_OK);
    if ($uploadError !== UPLOAD_ERR_OK) {
        $messages = [
            UPLOAD_ERR_INI_SIZE => 'Görsel sunucunun izin verdiği dosya boyutunu aşıyor.',
            UPLOAD_ERR_FORM_SIZE => 'Görsel formun izin verdiği dosya boyutunu aşıyor.',
            UPLOAD_ERR_PARTIAL => 'Görselin yüklenmesi yarıda kaldı. Lütfen tekrar deneyin.',
            UPLOAD_ERR_NO_TMP_DIR => 'Sunucuda geçici yükleme klasörü bulunamadı.',
            UPLOAD_ERR_CANT_WRITE => 'Sunucu yüklenen görseli diske yazamadı.',
            UPLOAD_ERR_EXTENSION => 'Sunucudaki bir PHP eklentisi yüklemeyi durdurdu.',
        ];
        throw new RuntimeException($messages[$uploadError] ?? ('Görsel yüklenemedi. Hata kodu: ' . $uploadError));
    }

    $tmpName = (string)($file['tmp_name'] ?? '');
    if ($tmpName === '' || !is_uploaded_file($tmpName)) {
        throw new RuntimeException('Yüklenen görsel doğrulanamadı. Lütfen dosyayı yeniden seçin.');
    }

    $maxBytes = (int)($config['upload']['max_bytes'] ?? 4 * 1024 * 1024);
    $size = (int)($file['size'] ?? 0);
    if ($size <= 0) {
        throw new RuntimeException('Boş görsel yüklenemez.');
    }
    if ($size > $maxBytes) {
        $maxMb = number_format($maxBytes / 1024 / 1024, 1, ',', '');
        throw new RuntimeException("Görsel en fazla {$maxMb} MB olabilir.");
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = (string)$finfo->file($tmpName);
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
        try {
            return vercel_blob_put("{$scope}/{$filename}", $tmpName, $mime, $config);
        } catch (Throwable $blobError) {
            // Domain yayını sırasında storage ayarı anlık sorun çıkarsa kullanıcı yüklemesi kaybolmasın.
            // Production fallback görseli harici MySQL/TiDB içinde saklar; container filesystem'e güvenmez.
            if ((bool)($config['storage']['database_fallback'] ?? true)) {
                try {
                    return database_image_put($scope, $filename, $tmpName, $mime);
                } catch (Throwable $dbError) {
                    throw new RuntimeException(
                        'Görsel depolanamadı. Blob: ' . $blobError->getMessage() . ' | Veritabanı yedeği: ' . $dbError->getMessage()
                    );
                }
            }
            throw $blobError;
        }
    }

    if ($driver === 'database') {
        return database_image_put($scope, $filename, $tmpName, $mime);
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
    if (!move_uploaded_file($tmpName, $full)) {
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

    $dbImageId = database_image_id_from_path($path);
    if ($dbImageId > 0) {
        database_image_delete($dbImageId);
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

function database_image_put(string $scope, string $filename, string $tmpFile, string $mime): string {
    global $pdo;
    if (!($pdo instanceof PDO)) {
        throw new RuntimeException('Veritabanı bağlantısı hazır değil.');
    }

    ensure_media_uploads_table($pdo);
    $body = file_get_contents($tmpFile);
    if ($body === false) {
        throw new RuntimeException('Yüklenen dosya okunamadı.');
    }

    $q = $pdo->prepare('INSERT INTO media_uploads(scope, filename, mime_type, size_bytes, data) VALUES(?,?,?,?,?)');
    $q->bindValue(1, $scope);
    $q->bindValue(2, $filename);
    $q->bindValue(3, $mime);
    $q->bindValue(4, strlen($body), PDO::PARAM_INT);
    $q->bindValue(5, $body, PDO::PARAM_LOB);
    $q->execute();

    $id = (int)$pdo->lastInsertId();
    if ($id <= 0) {
        throw new RuntimeException('Görsel veritabanına kaydedilemedi.');
    }
    return '/media.php?id=' . $id;
}

function ensure_media_uploads_table(PDO $pdo): void {
    static $ready = false;
    if ($ready) return;

    $pdo->exec("CREATE TABLE IF NOT EXISTS media_uploads (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        scope VARCHAR(20) NOT NULL,
        filename VARCHAR(190) NOT NULL,
        mime_type VARCHAR(80) NOT NULL,
        size_bytes INT UNSIGNED NOT NULL DEFAULT 0,
        data MEDIUMBLOB NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_media_scope_created (scope, created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $ready = true;
}

function database_image_id_from_path(string $path): int {
    $query = parse_url($path, PHP_URL_QUERY);
    $pathOnly = parse_url($path, PHP_URL_PATH);
    if ($pathOnly !== '/media.php' || !is_string($query)) return 0;

    parse_str($query, $params);
    return max(0, (int)($params['id'] ?? 0));
}

function database_image_delete(int $id): void {
    global $pdo;
    if ($id <= 0 || !($pdo instanceof PDO)) return;

    try {
        ensure_media_uploads_table($pdo);
        $q = $pdo->prepare('DELETE FROM media_uploads WHERE id=?');
        $q->execute([$id]);
    } catch (Throwable) {
        // Görsel silme hatası ana kayıt işlemini engellemesin.
    }
}

function vercel_blob_put(string $pathname, string $tmpFile, string $mime, array $config): string {
    if (!extension_loaded('curl')) {
        throw new RuntimeException('Vercel Blob için PHP cURL eklentisi gerekli.');
    }

    [$headers, $apiUrl] = vercel_blob_auth($config);
    $url = $apiUrl . '/?pathname=' . rawurlencode($pathname);
    $body = file_get_contents($tmpFile);
    if ($body === false) throw new RuntimeException('Yüklenen dosya okunamadı.');

    // @vercel/blob SDK v2 davranışıyla aynı temel header seti.
    $headers[] = 'Content-Type: application/octet-stream';
    $headers[] = 'x-content-type: ' . $mime;
    $headers[] = 'x-vercel-blob-access: public';
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
        $suffix = $message !== '' ? ': ' . $message : ' (HTTP ' . $status . ').';
        throw new RuntimeException('Vercel Blob yükleme hatası' . $suffix);
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
    $configuredStoreId = trim((string)($storage['blob_store_id'] ?? ''));
    $apiUrl = rtrim((string)($storage['blob_api_url'] ?? 'https://vercel.com/api/blob'), '/');

    // Güncel @vercel/blob davranışı: Vercel ortamında OIDC + store ID varsa onu kullan,
    // aksi halde read-write token içindeki store ID ile doğrula.
    if ($oidcToken !== '' && $configuredStoreId !== '') {
        return [[
            'Authorization: Bearer ' . $oidcToken,
            'x-vercel-blob-store-id: ' . normalize_blob_store_id($configuredStoreId),
        ], $apiUrl];
    }

    if ($token !== '') {
        $parts = explode('_', $token);
        $tokenStoreId = normalize_blob_store_id((string)($parts[3] ?? ''));
        if ($tokenStoreId === '') {
            throw new RuntimeException('BLOB_READ_WRITE_TOKEN içinden Blob Store ID okunamadı.');
        }
        return [[
            'Authorization: Bearer ' . $token,
            'x-vercel-blob-store-id: ' . $tokenStoreId,
        ], $apiUrl];
    }

    throw new RuntimeException('Vercel Blob bağlı değil. BLOB_READ_WRITE_TOKEN veya VERCEL_OIDC_TOKEN + BLOB_STORE_ID gerekli.');
}

function normalize_blob_store_id(string $storeId): string {
    $storeId = trim($storeId);
    return str_starts_with($storeId, 'store_') ? substr($storeId, 6) : $storeId;
}
