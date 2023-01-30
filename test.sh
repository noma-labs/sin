#!/bin/bash
cd sin

docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-req=ext-gd --ignore-platform-req=ext-exif


./vendor/bin/sail  up -d

## Create the database
docker compose exec laravel.test php artisan make:database db_scuola db_scuola
docker compose exec laravel.test php artisan make:database db_anagrafe db_anagrafe

docker compose exec laravel.test php /var/www/html/vendor/bin/phpunit  --configuration /var/www/html/phpunit.xml --testsuite Unit --testdox "$@"
docker compose exec laravel.test php /var/www/html/vendor/bin/phpunit  --configuration /var/www/html/phpunit.xml --testsuite Http --testdox "$@"
