#!/usr/bin/env bash
set -e

PORT="${PORT:-80}"

# Apache port ayarını Render'ın verdiği PORT değişkenine göre ayarla
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# MariaDB Başlat
service mariadb start || true

# Veritabanını oluştur ve live veriyi içeri aktar
mysql -e "CREATE DATABASE IF NOT EXISTS \`kebapzade_menu\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" || true
mysql -e "CREATE USER IF NOT EXISTS 'kebapzade'@'localhost' IDENTIFIED BY 'kebapzade123';" || true
mysql -e "CREATE USER IF NOT EXISTS 'kebapzade'@'127.0.0.1' IDENTIFIED BY 'kebapzade123';" || true
mysql -e "GRANT ALL PRIVILEGES ON \`kebapzade_menu\`.* TO 'kebapzade'@'localhost';" || true
mysql -e "GRANT ALL PRIVILEGES ON \`kebapzade_menu\`.* TO 'kebapzade'@'127.0.0.1';" || true
mysql -e "FLUSH PRIVILEGES;" || true

# Tabloları kontrol et, yoksa database/kebapzade_live.sql'den yükle
TABLE_COUNT=$(mysql -u kebapzade -pkebapzade123 kebapzade_menu -sse "SELECT count(*) FROM information_schema.tables WHERE table_schema='kebapzade_menu';" 2>/dev/null || echo "0")

if [ "$TABLE_COUNT" -eq "0" ]; then
    echo "Veritabanı tabloları içe aktarılıyor..."
    if [ -f /var/www/html/database/kebapzade_live.sql ]; then
        mysql -u kebapzade -pkebapzade123 kebapzade_menu < /var/www/html/database/kebapzade_live.sql || true
    fi
fi

# Admin şifresini 'admin123' olarak garantiye al
ADMIN_HASH='$2y$10$wK1kK/q028eR61bZvhK3EOnv9H9gYV7R8W4jC7YJgJ2V.WnFvKjKy'
mysql -u kebapzade -pkebapzade123 kebapzade_menu -e "UPDATE admins SET password_hash='\$2y\$10\$Y3wGzVv7TKnS.rG8n9kEPeZzV6uL0q5M7Z7qOqFpX8h9x3Qp3k9KG' WHERE id=1;" 2>/dev/null || true

# Apache'yi ön planda çalıştır
exec apache2-foreground
