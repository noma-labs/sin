#!/bin/bash

cd sin || exit

./vendor/bin/sail up -d

# give some time to start up te database
sleep 5

# create the databases (if missing)
./vendor/bin/sail artisan make:database
