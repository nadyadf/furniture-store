FROM php:8.2-fpm-alpine

# Install ekstensi PHP & dependensi yang dibutuhkan CI4
RUN apk add --no-cache \
    nginx \
    icu-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libxml2-dev \
    supervisor && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo pdo_mysql mysqli intl mbstring gd xml

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy semua file project
COPY . .

# Install dependensi via Composer
RUN composer install --no-dev --optimize-autoloader

# Set permission untuk folder writable CI4
RUN chown -R www-data:www-data /var/www/html/writable /var/www/html/public

# Konfigurasi Nginx sederhana
RUN echo 'server { \
    listen 8080; \
    root /var/www/html/public; \
    index index.php index.html; \
    location / { \
        try_files $uri $uri/ /index.php?$query_string; \
    } \
    location ~ \.php$ { \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_index index.php; \
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \
        include fastcgi_params; \
    } \
}' > /etc/nginx/http.d/default.conf

# Script runner untuk menjalankan PHP-FPM dan Nginx bersamaan
CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]