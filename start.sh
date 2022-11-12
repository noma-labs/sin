#!/bin/bash


#alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'
cd sin

docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs

./vendor/bin/sail up

sleep 5


./vendor/bin/sail artisan make:database db_admsys
./vendor/bin/sail artisan make:database db_nomadelfia
./vendor/bin/sail artisan make:database archivio_biblioteca
./vendor/bin/sail artisan make:database db_scuola db_scuola
./vendor/bin/sail artisan make:database db_anagrafe db_anagrafe
./vendor/bin/sail artisan make:database db_patente db_patente
./vendor/bin/sail artisan make:database db_meccanica db_meccanica