FROM php:8.4-fpm

# Install system dependencies and PDO MySQL extension
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && pecl install redis && docker-php-ext-enable redis

# Optional: Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
