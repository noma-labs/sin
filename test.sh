#!/bin/bash
docker-compose up -d app
docker-compose exec app php ./vendor/bin/phpunit --testdox