#!/bin/bash

# THE ORDER IS IMPORTANT
docker-compose exec app php artisan migrate:fresh --path="database/migrations/admsys" --database=db_auth
docker-compose exec app php artisan migrate:fresh --path="database/migrations/db_nomadelfia" --database=db_nomadelfia
docker-compose exec app php artisan migrate:fresh --path="database/migrations/biblioteca" --database=db_biblioteca
