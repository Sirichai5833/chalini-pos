FROM php:8.2-apache

# Install system & PHP deps
RUN apt-get update && apt-get install -y \
    git zip unzip curl \
    libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# ❗ ปิด MPM ทุกตัวก่อน
RUN a2dismod mpm_event mpm_worker mpm_prefork || true

# ✅ เปิดแค่ prefork
RUN a2enmod mpm_prefork rewrite

# Set Apache document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN cp .env.example .env \
    && php artisan key:generate \
    && php artisan optimize:clear \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]
