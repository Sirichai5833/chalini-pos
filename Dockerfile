FROM php:8.2-fpm

# ติดตั้ง dependencies และ PHP extensions ที่ Laravel ต้องใช้
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    git \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# ติดตั้ง Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# กำหนด working directory
WORKDIR /var/www

# คัดลอกไฟล์ทั้งหมดเข้า container
COPY . .

# ติดตั้ง Laravel dependencies
RUN composer install

CMD ["php-fpm"]
