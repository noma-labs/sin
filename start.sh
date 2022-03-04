#!/bin/bash
#docker-compose up -d
#
#sleep 5
#
#docker-compose ps

alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'
sail up

sleep 5


sail artisan make:database db_admsys
sail artisan make:database db_nomadelfia
sail artisan make:database archivio_biblioteca
sail artisan make:database db_scuola db_scuola
sail artisan make:database db_anagrafe db_anagrafe
sail artisan make:database db_patente db_patente
sail artisan make:database db_meccanica db_meccanica