
#!/bin/bash
cd sin

./vendor/bin/sail  up -d

#docker-compose up -d app
#
## Create the database
docker-compose exec laravel.test php artisan make:database db_scuola db_scuola
docker-compose exec laravel.test php artisan make:database db_anagrafe db_anagrafe

docker-compose exec laravel.test php /var/www/html/vendor/bin/phpunit  --configuration /var/www/html/phpunit.xml --testsuite Unit --testdox "$@"
#docker-compose exec laravel.test php /var/www/html/vendor/bin/phpunit  --configuration /var/www/html/phpunit.xml --testsuite Http --testdox