
#!/bin/bash
docker-compose up -d app

# Create the database
docker-compose exec app php artisan make:database db_scuola db_scuola
docker-compose exec app php artisan make:database db_anagrafe db_anagrafe

#docker-compose exec app php ./vendor/bin/phpunit  --configuration /var/www/phpunit.xml --testsuite Actions --testdox
docker-compose exec app php ./vendor/bin/phpunit  --configuration /var/www/phpunit.xml --testsuite Unit --testdox --filter testAzienda

#docker-compose exec app php ./vendor/bin/phpunit  --configuration /var/www/phpunit.xml --testsuite Feature --testdox
#docker-compose exec app php ./vendor/bin/phpunit  --configuration /var/www/phpunit.xml --testsuite Http --testdox