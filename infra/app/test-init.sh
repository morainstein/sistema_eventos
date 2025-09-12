#!/bin/sh

php artisan optimize:clear --silent
php artisan migrate --force --env=testing 
php artisan test --coverage

echo "Script de testes finalizado \n"
echo "Rode o script: './app/prod-init.sh' para iniciar o ambiente de produção\n"