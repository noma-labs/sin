#!/bin/bash

cd sin

# Create the database
./vendor/bin/sail artisan make:database db_admsys
./vendor/bin/sail artisan make:database db_nomadelfia
./vendor/bin/sail artisan make:database archivio_biblioteca
./vendor/bin/sail artisan make:database db_scuola db_scuola
./vendor/bin/sail artisan make:database db_anagrafe db_anagrafe
./vendor/bin/sail artisan make:database db_patente db_patente
./vendor/bin/sail artisan make:database db_meccanica db_meccanica


# THE ORDER IS IMPORTANT
./vendor/bin/sail artisan migrate:fresh --path="database/migrations/admsys" --database=db_auth
./vendor/bin/sail artisan migrate:fresh --path="database/migrations/angrafe" --database=db_anagrafe
./vendor/bin/sail artisan migrate:fresh --path="database/migrations/db_nomadelfia" --database=db_nomadelfia
./vendor/bin/sail artisan migrate:fresh --path="database/migrations/biblioteca" --database=db_biblioteca
./vendor/bin/sail artisan migrate:fresh --path="database/migrations/scuola" --database=db_scuola
./vendor/bin/sail artisan migrate:refresh --path="database/migrations/patente" --database=db_patente
./vendor/bin/sail artisan migrate:refresh --path="database/migrations/officina" --database=db_officina
./vendor/bin/sail artisan migrate # migrate the migrations outside the folder (like the activity_log)

# import dump of a db_nomadelfia
DB_CONTAINER=$(docker compose ps -q mysql)
docker exec -i  $DB_CONTAINER sh -c 'exec mysql -uroot -proot db_nomadelfia' < ../sql/db_nomadelfia.sql
echo "import succesfully"

# seed tables
./vendor/bin/sail artisan db:seed --class=AuthTablesSeeder
./vendor/bin/sail artisan db:seed --class=CaricheTableSeeder
./vendor/bin/sail artisan db:seed --class=IncarichiTableSeeder
./vendor/bin/sail artisan db:seed --class=ScuolaTableSeeder
./vendor/bin/sail artisan db:seed --class=BibliotecaTableSeeder
./vendor/bin/sail artisan db:seed --class=VeicoloTableSeeder