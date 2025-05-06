#!/bin/sh

echo "⏳ Esperando a que la base de datos MySQL esté lista..."

while ! nc -z db 3306; do
  sleep 1
done

echo "✅ Base de datos lista, continuando..."
exec "$@"