FROM php:8.2-apache

# Install system & PHP deps
RUN apt-get update && apt-get install -y \
    git zip unzip curl \
    libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Enable Apache rewrite
RUN a2enmod rewrite

# Set Apache document root to Laravel public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Workdir
WORKDIR /var/www/html

# Copy project
COPY . .

# Install Laravel deps
RUN composer install --no-dev --optimize-autoloader

# Laravel permissions + key
RUN cp .env.example .env \
    && php artisan key:generate \
    && php artisan optimize:clear \
    && chown -R www-data:www-data storage bootstrap/cache

# Railway uses PORT env
EXPOSE 80

# ⭐ สำคัญที่สุด (ไม่มีอันนี้ = fail)
CMD ["apache2-foreground"]
