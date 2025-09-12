#!/bin/sh
set -e

php artisan optimize
php artisan queue:work
