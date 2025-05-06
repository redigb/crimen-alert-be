# PHP FPM oficial
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    nginx \
    git \
    unzip \
    libzip-dev \
    netcat-openbsd \
    libpq-dev \
    zip \
    && docker-php-ext-install pdo_mysql zip

# Install composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
