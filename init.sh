#!/bin/bash

cd sin || exit

# install the composer dependencies
docker run --rm \
   -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install  --ignore-platform-req=ext-gd --ignore-platform-req=ext-exif

# install the frontend dependencies
./vendor/bin/sail npm install
./vendor/bin/sail npm run production