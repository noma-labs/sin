
#!/bin/bash
docker-compose up -d app
#docker-compose exec app php ./vendor/bin/phpunit  --configuration /var/www/phpunit.xml --testsuite Actions --testdox
#docker-compose exec app php ./vendor/bin/phpunit  --configuration /var/www/phpunit.xml --testsuite Unit --testdox
docker-compose exec app php ./vendor/bin/phpunit  --configuration /var/www/phpunit.xml --testsuite Feature --testdox
#docker-compose exec app php ./vendor/bin/phpunit  --configuration /var/www/phpunit.xml --testsuite Http --testdox