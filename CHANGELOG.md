# Changelog

## V1.1
- Online reservation request form added.
- Reservation status management added to admin.
- Reservation CSV export added.
- Outdoor seating, fireplace and private dining service cards added.
- Hero and story image uploads added to Site Settings.
- Mobile Call / WhatsApp / Reservation action bar added.
- Multi-admin user management added.
- QR menu landing and print screen added.
- Schema.org Restaurant structured data added.
- OpenGraph, canonical, sitemap and robots added.
- Existing V1 installations can upgrade with `update-v1.1.php` without deleting menu data.
# V2.0
- Kebapzade logolu TR/EN karşılama ekranı eklendi.
- Fotoğraflı, akordeon yapılı mobil QR menü hazırlandı.
- Menü araması ve mobil hızlı işlem çubuğu eklendi.
- Admin paneline logo ve kategori kapak görseli yönetimi eklendi.
- Mevcut V1.1 verilerini koruyan `update-v2.php` yükseltmesi eklendi.


## 2026-09-30 — Production image upload & social update
- Vercel Blob upload headers updated for current Store ID and public-access requirements.
- Database-backed image fallback added for Vercel/container deployments.
- Friday afternoon opening notice added to welcome and QR menu screens.
- Instagram CTA added below the QR menu CTA, defaulting to @kebapzaderestaurant.
- Hard-coded database credentials removed from config defaults; production must use environment variables.

### 2026-09-30 UI refinement
- Friday opening notice changed to a high-contrast cream/gold panel with dark brown text.
- Instagram CTA redesigned from pink to a compact Kebapzade brown/gold treatment with Instagram icon and text.
- QR menu Friday notice updated to use the same readable cream/gold treatment.
