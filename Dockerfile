FROM php:8.2-cli

# Install system & PHP deps
RUN apt-get update && apt-get install -y \
    git zip unzip curl \
    libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN cp .env.example .env \
    && php artisan key:generate \
    && php artisan optimize:clear \
    && chown -R www-data:www-data storage bootstrap/cache

# Railway จะส่ง PORT มาให้
CMD php artisan serve --host=0.0.0.0 --port=${PORT}
