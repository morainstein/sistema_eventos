#!/bin/sh
set -e

php artisan migrate --force --seed --graceful
php artisan optimize

echo "script de produção executado!"