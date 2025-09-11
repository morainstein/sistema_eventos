#!/bin/sh
set -e

# Permissão pro php-fpm
# chown -R www-data:www-data /app/storage /app/bootstrap/cache

php artisan migrate --force
php artisan optimize

echo "script de produção executado!"