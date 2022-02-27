#!/bin/bash

# Create the database
docker-compose exec app php artisan make:database db_scuola db_scuola
docker-compose exec app php artisan make:database db_anagrafe db_anagrafe
docker-compose exec app php artisan make:database db_patente db_patente
docker-compose exec app php artisan make:database db_meccanica db_meccanica


# THE ORDER IS IMPORTANT
docker-compose exec app php artisan migrate:fresh --path="database/migrations/admsys" --database=db_auth
docker-compose exec app php artisan migrate:fresh --path="database/migrations/angrafe" --database=db_anagrafe
docker-compose exec app php artisan migrate:fresh --path="database/migrations/db_nomadelfia" --database=db_nomadelfia
docker-compose exec app php artisan migrate:fresh --path="database/migrations/biblioteca" --database=db_biblioteca
docker-compose exec app php artisan migrate:fresh --path="database/migrations/scuola" --database=db_scuola
docker-compose exec app php artisan migrate:refresh --path="database/migrations/patente" --database=db_patente
#docker-compose exec app php artisan migrate:fresh --path="database/migrations/officina" --database=db_officina
docker-compose exec app php artisan migrate # migrate the migrations outside the folder (like the activity_log)

# import dump of a db_nomadelfia
DB_CONTAINER=$(docker-compose ps -q db)
docker exec -i  $DB_CONTAINER sh -c 'exec mysql -uroot -proot db_nomadelfia' < ./sql/db_nomadelfia.sql
echo "import succesfully"

# seed tables
docker-compose exec app php artisan db:seed --class=AuthTablesSeeder
docker-compose exec app php artisan db:seed --class=CaricheTableSeeder
docker-compose exec app php artisan db:seed --class=IncarichiTableSeeder
docker-compose exec app php artisan db:seed --class=ScuolaTableSeeder
docker-compose exec app php artisan db:seed --class=BibliotecaTableSeeder