#!/bin/bash
#docker-compose up -d
#
#sleep 5
#
#docker-compose ps

#alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'
cd sin

./vendor/bin/sail up

sleep 5


./vendor/bin/sail artisan make:database db_admsys
./vendor/bin/sail artisan make:database db_nomadelfia
./vendor/bin/sail artisan make:database archivio_biblioteca
./vendor/bin/sail artisan make:database db_scuola db_scuola
./vendor/bin/sail artisan make:database db_anagrafe db_anagrafe
./vendor/bin/sail artisan make:database db_patente db_patente
./vendor/bin/sail artisan make:database db_meccanica db_meccanica