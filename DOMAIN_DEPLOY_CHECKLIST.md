# Domain Yayını — Son Kontrol Listesi (30.09.2026)

## Vercel Environment Variables

Aşağıdakileri Production ortamında doğrulayın:

```text
APP_URL=https://ALANADINIZ
APP_TIMEZONE=Europe/Istanbul
APP_DEBUG=false
DATABASE_URL=mysql://USER:PASSWORD@HOST:PORT/DATABASE
DB_SSL=true
SESSION_DRIVER=database
SESSION_AUTO_MIGRATE=true
SESSION_SECURE_COOKIE=true
APP_STORAGE_DRIVER=vercel_blob
STORAGE_DATABASE_FALLBACK=true
UPLOAD_MAX_BYTES=4194304
```

Blob store **Public** olmalı ve projeye bağlı olmalı. Yeni Vercel bağlantısında `VERCEL_OIDC_TOKEN` + `BLOB_STORE_ID`; eski bağlantıda `BLOB_READ_WRITE_TOKEN` kullanılabilir.

## Deploy sonrası 5 dakikalık smoke test

1. `/health.php` açın; `db` connected, `pdo_mysql`, `fileinfo` ve `curl` true olmalı.
2. `/admin/login.php` ile giriş yapın.
3. Bir ürüne JPG/PNG/WEBP fotoğraf yükleyin, kaydedin ve tekrar düzenleme ekranında önizlemenin geldiğini doğrulayın.
4. `/` sayfasında QR Menü butonunun altında Instagram butonunu kontrol edin.
5. `/` ve `/qr-menu.php` üzerinde “Cuma günleri öğleden sonra açığız.” bilgisini kontrol edin.
6. `/qr-menu.php` üzerinden yüklenen ürün görselinin misafir tarafında göründüğünü doğrulayın.
7. Vercel > Project > Domains alanında custom domain için SSL sertifikasının `Valid` olduğunu doğrulayın.

## Fotoğraf yükleme davranışı

- Öncelik: Vercel Blob.
- Blob isteği başarısız olursa: `STORAGE_DATABASE_FALLBACK=true` sayesinde `media_uploads` tablosuna kalıcı kayıt.
- Vercel container içindeki `/uploads` klasörü production kalıcılığı için kullanılmaz.

## Güvenlik

Kaynak kodda canlı DB parolası tutulmuyor. Daha önce repo/ZIP içinde paylaşılmış eski DB parolası varsa yayına çıkmadan önce DB sağlayıcısından parolayı rotate edin ve yeni değeri Vercel Environment Variables'a yazın.
