FROM php:8.2-apache

# Gerekli sistem paketleri ve PHP eklentileri (pdo_mysql, sqlite3, fileinfo)
RUN apt-get update && apt-get install -y \
    mariadb-server \
    mariadb-client \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite fileinfo \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# Apache yapılandırması
RUN sed -ri -e 's!/var/www/html!/var/www/html!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf

WORKDIR /var/www/html

# Proje dosyalarını kopyala
COPY . /var/www/html/

# uploads dizini izinleri
RUN mkdir -p /var/www/html/uploads/menu /var/www/html/uploads/site \
    && chown -R www-data:www-data /var/www/html/uploads \
    && chmod -R 775 /var/www/html/uploads

# Başlatma scripti
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

CMD ["/usr/local/bin/entrypoint.sh"]
