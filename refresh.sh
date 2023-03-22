#!/bin/bash

# create the database if not exists
./vendor/bin/sail artisan make:database

# run the migrations
# NOTE: THE ORDER IS IMPORTANT !!"
./vendor/bin/sail artisan migrate:fresh --path="database/migrations/admsys" --database=db_auth
./vendor/bin/sail artisan migrate:fresh --path="database/migrations/db_nomadelfia" --database=db_nomadelfia
./vendor/bin/sail artisan migrate:fresh --path="database/migrations/scuola" --database=db_scuola
./vendor/bin/sail artisan migrate:fresh --path="database/migrations/biblioteca" --database=db_biblioteca
./vendor/bin/sail artisan migrate:fresh --path="database/migrations/patente" --database=db_patente
./vendor/bin/sail artisan migrate:fresh --path="database/migrations/officina" --database=db_officina

# seed tables
./vendor/bin/sail artisan db:seed