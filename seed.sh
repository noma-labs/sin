#!/bin/bash


# import dump of a db_nomadelfia
DB_CONTAINER=$(docker-compose ps -q db)
docker exec -i  $DB_CONTAINER sh -c 'exec mysql -uroot -proot db_nomadelfia' < ./sql/db_nomadelfia.sql
echo "import succesfully"

# seed the auth tables for authentication
docker-compose exec app php artisan db:seed --class=AuthTablesSeeder
