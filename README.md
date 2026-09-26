# Kebapzade QR Menü V2
Saf PHP + MySQL ile hazırlanmış TR/EN karşılama ekranı, fotoğraflı QR menü ve yönetim paneli.

## V2 Özellikleri
- Kebapzade logolu karşılama ekranı
- Türkçe / İngilizce QR menü
- Büyük fotoğraflı kategori kartları ve açılır ürün listeleri
- Canlı yemek / içerik arama
- Ürün fotoğrafı, açıklama, fiyat ve porsiyon varyantları
- Öne çıkan / acılı / vejetaryen etiketleri
- Online rezervasyon talep formu
- Admin'de rezervasyon durumu: Bekliyor / Onaylandı / İptal / Tamamlandı
- Birden fazla yönetici hesabı oluşturma ve şifre yönetimi
- Rezervasyon CSV dışa aktarma
- Açık hava / şömine / özel yemek odası hizmet bölümü
- Mobilde sabit Ara / WhatsApp / Masa Ayırt aksiyonları
- Hero ve hikâye görselini admin'den yükleme
- Logo ve kategori kapak görsellerini admin'den yükleme
- QR menü yönlendirmesi ve admin'de QR baskı ekranı
- Restaurant Schema.org JSON-LD, canonical, OpenGraph, sitemap ve robots
- CSRF, PDO prepared statement, güvenli görsel yükleme ve parola hash altyapısı

## Gereksinimler
- PHP 8.1+
- MySQL 5.7+ veya MariaDB 10.4+
- PDO MySQL
- fileinfo

## Sıfırdan Kurulum
1. Dosyaları web köküne yükleyin.
2. Boş MySQL veritabanı ve kullanıcı oluşturun.
3. `config/config.php` veritabanı bilgilerini girin.
4. `app.base_url` alanına canlı site adresini yazın. Örn: `https://kebapzade.com`
5. `/install.php` adresini açıp ilk admin hesabını oluşturun.
6. Kurulumdan sonra **install.php dosyasını silin**.
7. `/admin/login.php` üzerinden giriş yapın.

## V1.1'den V2'ye Güncelleme
1. V2 dosyalarını V1.1'in üzerine yükleyin.
2. Admin hesabınızla giriş yapın.
3. `/update-v2.php` adresini açıp güncellemeyi çalıştırın.
4. Tamamlandıktan sonra **update-v2.php dosyasını silin**.

Mevcut ürün, kategori ve fiyat verileri silinmez.

## Önemli URL'ler
- Karşılama: `/`
- Dijital menü: `/qr-menu.php`
- Eski tam restoran sayfası: `/restaurant.php`
- QR hedefi: `/qr-menu.php`
- Admin: `/admin/login.php`
- Rezervasyonlar: `/admin/reservations.php`
- QR baskı ekranı: `/admin/qr-menu.php`

## Not
Mevcut Kebapzade menüsünden oluşturulan başlangıç verisinde fiyatlar büyük ölçüde boş bırakılmıştır. Fiyatları admin panelinden güncelleyin.


## Vercel Deploy
Bu proje Vercel container runtime için güncellenmiştir. Ayrıntılı kurulum için `VERCEL_DEPLOY.md` dosyasına bakın.
