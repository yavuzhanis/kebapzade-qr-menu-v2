# Kebapzade QR Menü V2 — Vercel Deploy

Bu sürüm Vercel'in `Dockerfile.vercel` container runtime modeline göre hazırlanmıştır.

## 1) Kalıcı MySQL oluşturun

Vercel container içinde MariaDB çalıştırılmaz. Harici/managed bir MySQL servisi kullanın.

Mevcut veriyi taşıyacaksanız `database/kebapzade_live.sql` dosyasını yeni MySQL'e manuel import edin. Bu dump'taki eski admin kaydı güvenlik amacıyla kaldırılmıştır.

Yeni/boş kurulumda `install.php` şemayı ve başlangıç menüsünü oluşturabilir.

## 2) Vercel Blob bağlayın

Vercel Dashboard > Project > Storage üzerinden **Public Blob** store oluşturup projeye bağlayın. Menü görselleri herkese açık olduğundan public store kullanılmalıdır.

Kod iki kimlik doğrulama biçimini destekler:

- `BLOB_READ_WRITE_TOKEN`
- `VERCEL_OIDC_TOKEN` + `BLOB_STORE_ID`

`STORAGE_DRIVER=vercel_blob` kullanın.

## 3) Environment Variables

Minimum production ayarları:

```text
APP_NAME=Kebapzade Premium
APP_URL=https://alanadiniz.com
APP_TIMEZONE=Europe/Istanbul
APP_DEBUG=false

DATABASE_URL=mysql://USER:PASSWORD@HOST:3306/DATABASE

SESSION_DRIVER=database
SESSION_LIFETIME=7200
SESSION_AUTO_MIGRATE=true
SESSION_SECURE_COOKIE=true

STORAGE_DRIVER=vercel_blob
UPLOAD_MAX_BYTES=4194304
```

`DATABASE_URL` yerine `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` ayrı ayrı da verilebilir.

## 4) İlk admin hesabı

Boş veritabanında geçici olarak güçlü ve rastgele bir `INSTALL_TOKEN` environment variable ekleyin ve deploy edin.

Ardından:

```text
https://alanadiniz.com/install.php?token=INSTALL_TOKEN_DEGERI
```

adresinden ilk admin hesabını oluşturun.

Kurulum biter bitmez `INSTALL_TOKEN` environment variable'ını Vercel'den kaldırın ve tekrar deploy edin. Token yokken `install.php` 404 döndürür.

Mevcut DB'de zaten admin hesabı varsa bu adım gerekmez.

## 5) Deploy

Repo root'unda şu dosyalar Vercel tarafından kullanılır:

- `Dockerfile.vercel`
- `vercel-entrypoint.sh`
- `vercel.json`

GitHub reposunu Vercel'e import ederek veya Vercel CLI ile deploy edebilirsiniz.

## Production notları

- Container filesystem kalıcı değildir. Yeni admin görsel yüklemeleri Vercel Blob'a gider.
- PHP session verileri `app_sessions` tablosunda MySQL'e yazılır.
- `SESSION_AUTO_MIGRATE=true` ise session tablosu eksik olduğunda otomatik oluşturulur. İsterseniz `database/vercel_upgrade.sql` dosyasını manuel çalıştırıp bunu `false` yapabilirsiniz.
- `APP_DEBUG=false` bırakın.
- Eski Render DB/admin şifreleri bu sürümden çıkarılmıştır; daha önce public repoda bulunduysa ilgili şifreleri değiştirin.
- Vercel istek payload sınırı nedeniyle varsayılan görsel limiti 4 MB'dır. Daha büyük görseller için client-side Blob upload mimarisi gerekir.
