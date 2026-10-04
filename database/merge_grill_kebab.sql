-- Kebapzade QR Menü: Izgara ve Kebap kategorilerini tek kategoride birleştirme

-- 1. Kebaplar kategorisindeki tüm ürünleri Izgaralar kategorisine aktar
UPDATE `items` 
SET `category_id` = (SELECT `id` FROM (SELECT `id` FROM `categories` WHERE `slug` = 'izgaralar' OR `slug` = 'izgara-kebaplar' LIMIT 1) as t) 
WHERE `category_id` = (SELECT `id` FROM (SELECT `id` FROM `categories` WHERE `slug` = 'kebaplar' LIMIT 1) as t2);

-- 2. Kategori başlığını 'Izgara & Kebaplar' olarak güncelle
UPDATE `categories` 
SET `name_tr` = 'Izgara & Kebaplar', 
    `name_en` = 'Grills & Kebabs', 
    `slug` = 'izgara-kebaplar' 
WHERE `slug` = 'izgaralar' OR `slug` = 'izgara-kebaplar';

-- 3. Artık boş olan 'kebaplar' kategorisini sil
DELETE FROM `categories` WHERE `slug` = 'kebaplar';
