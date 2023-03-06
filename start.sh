#!/bin/bash

cd sin || exit

./vendor/bin/sail up

# give some time to start up te database
sleep 5

# create the databases (if missing)
sail artisan make:database
