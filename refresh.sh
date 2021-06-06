#!/bin/bash


docker-compose exec app php artisan migrate:refresh --path="database/migrations/admsys" --database=db_auth
docker-compose exec app php artisan migrate:refresh --path="database/migrations/db_nomadelfia" --database=db_nomadelfia
docker-compose exec app php artisan migrate:refresh --path="database/migrations/biblioteca" --database=db_biblioteca
