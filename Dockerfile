FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        bcmath \
        gd \
        zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Fix permissions
RUN chmod -R 775 storage bootstrap/cache

# Clear caches (ปลอดภัยบน Railway)
RUN php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear

# Railway จะส่ง PORT มาให้
EXPOSE 8080

# ✅ migrate ก่อน แล้วค่อย serve
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
