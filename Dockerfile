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

WORKDIR /var/www
COPY . .

# Instalar PHP dependencies
RUN composer install --no-dev --optimize-autoloader


# Permisos de carpetas importantes
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Exponer puerto
EXPOSE 3000

# Comando final: Migraciones, Seeders, y luego Servidor
CMD php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=3000