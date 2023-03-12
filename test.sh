#!/bin/bash
cd sin || exit

./vendor/bin/sail  up -d

docker compose exec laravel.test /var/www/html/vendor/bin/pest