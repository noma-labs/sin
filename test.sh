#!/bin/bash
cd sin || exit

./vendor/bin/sail  up -d

docker compose exec laravel.test php /var/www/html/vendor/bin/phpunit  --configuration /var/www/html/phpunit.xml --testsuite Unit --testdox "$@"
docker compose exec laravel.test php /var/www/html/vendor/bin/phpunit  --configuration /var/www/html/phpunit.xml --testsuite Http --testdox "$@"
