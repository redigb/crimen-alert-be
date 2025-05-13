#!/bin/bash

# Esperar a que la base de datos esté lista
until nc -z -v -w30 $DB_HOST 5432
do
  echo "Esperando a la base de datos..."
  sleep 5
done

# Migraciones y seeders
php artisan migrate --force
php artisan db:seed --force

# Iniciar servidor Laravel
php artisan serve --host=0.0.0.0 --port=3000