FROM php:8.2-cli

# 1. ติดตั้ง System Dependencies (เหมือนเดิม)
RUN apt-get update && apt-get install -y \
    git unzip zip curl \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-configure gd \
        --with-jpeg \
        --with-freetype \
    && docker-php-ext-install \
        pdo_mysql mbstring exif bcmath gd zip

# 2. ตั้งค่า Workdir และก๊อปปี้ไฟล์ (ต้องทำก่อนสั่ง artisan)
WORKDIR /var/www/html
COPY . .

# 3. ติดตั้ง Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# 4. จัดการ Permission และสร้าง Storage Link (ทำหลังจากก๊อปปี้ไฟล์เสร็จแล้ว)
RUN chmod -R 775 storage bootstrap/cache
RUN php artisan storage:link

EXPOSE 8080

CMD php artisan serve --host=0.0.0.0 --port=$PORT