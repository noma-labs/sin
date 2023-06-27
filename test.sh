#!/bin/bash

./vendor/bin/sail  up -d
#./vendor/bin/sail  php artisan key:generate --env=testing
./vendor/bin/sail  php artisan config:clear
./vendor/bin/sail  php artisan route:clear
./vendor/bin/sail  test --stop-on-error "$@"
