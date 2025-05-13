FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    netcat-openbsd \
    libpq-dev \
    zip \
    && docker-php-ext-install pdo_mysql zip

# Instalar Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

# Instalar dependencias
RUN composer install --no-dev --optimize-autoloader
RUN composer dump-autoload -o

# Permisos
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Copiar script de inicio
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 3000

CMD ["/entrypoint.sh"]
