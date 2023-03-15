#!/bin/bash
cd sin || exit

./vendor/bin/sail  up -d
./vendor/bin/sail  php artisan config:clear
./vendor/bin/sail  test
