#!/bin/bash

#alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'

# Create the database
sail artisan make:database db_scuola db_scuola
sail artisan make:database db_anagrafe db_anagrafe
sail artisan make:database db_patente db_patente
sail artisan make:database db_meccanica db_meccanica


# THE ORDER IS IMPORTANT
sail artisan migrate:fresh --path="database/migrations/admsys" --database=db_auth
sail artisan migrate:fresh --path="database/migrations/angrafe" --database=db_anagrafe
sail artisan migrate:fresh --path="database/migrations/db_nomadelfia" --database=db_nomadelfia
sail artisan migrate:fresh --path="database/migrations/biblioteca" --database=db_biblioteca
sail artisan migrate:fresh --path="database/migrations/scuola" --database=db_scuola
sail artisan migrate:refresh --path="database/migrations/patente" --database=db_patente
sail artisan migrate:refresh --path="database/migrations/officina" --database=db_officina
sail artisan migrate # migrate the migrations outside the folder (like the activity_log)

# import dump of a db_nomadelfia
DB_CONTAINER=$(docker-compose ps -q db)
docker exec -i  $DB_CONTAINER sh -c 'exec mysql -uroot -proot db_nomadelfia' < ./sql/db_nomadelfia.sql
echo "import succesfully"

# seed tables
sail artisan db:seed --class=AuthTablesSeeder
sail artisan db:seed --class=CaricheTableSeeder
sail artisan db:seed --class=IncarichiTableSeeder
sail artisan db:seed --class=ScuolaTableSeeder
sail artisan db:seed --class=BibliotecaTableSeeder