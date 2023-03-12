# SIN Nomadelfia
SIN (Sistema Informativo Nomadelfia) riunisce tutti i sistemi esistenti in Nomadelfia in un unico sistema.

### Dipendenze

Linguaggi:
   - *Php 8.1.x* 
   - *MariaDB 10.4.x*
   - *Node v16.X*

Framework:
  
  - backend
      - *Laravel 8.x*
  - frontend
      - *Vue.js 2.x*
      - *Bootstrap  4.x*
  
Other
  - *PhpMyAdmin : 5.2.0*
  - *npm 8.15.X*
  - *composer 2.4.x*

Required PHP extensions
  - gd
  - zip (require apt-get install libzip-dev, zip) -> required by spreedsheet 
  - exif                                          -> required by spatie/media-library
  - pdo_mysql             
  - mbstring                                      -> required by laravel sail  

## Installazione
**ATTENZIONE**: l'installazione di xampp elimina tutti i database e i siti nella cartella `C:/xampp/htdocs`. Per precauzione copiare la cartella prima di procedere con l'installazione di xampp.

1. Scarica ed installa [`xampp 7.1.10`](https://www.apachefriends.org/it/index.html) (include Apache 2.4.28, MariaDB 10.1.28, PHP 7.1.10, phpMyAdmin 4.7.4).

2. Scarica ed installa [`Composer`](https://getcomposer.org/download/). (Composer 1.8.5 2019-04-09) Composer è un tool a linea di comando che la gestione delle dipendenze PHP.

3. Scarica e instalal [`node.js v16.x`](https://nodejs.org/it/download/) (include `npm 8.15.x`)

4. Apri una shell (e.g. PowerShell o cmd) entra nella cartella `C:/xampp/htdocs`  e scarica la repository.
```
cd  C:/xampp/htdocs
git clone https://github.com/dido18/sistema-informativo-nomadelfia.git
```

5. Entra nella cartella ` C:/xampp/htdocs/sistema-informativo-nomadelfia/sin` e installa le dipendeze php con `composer` (installa le librerie leggendo il file _composer.json_):
```
cd C:/xampp/htdocs/sistema-informativo-nomadelfia/sin
composer install
```

6. Installa le dipendenze con `npm` (installa le dipendenze  front end leggendo il file _packages.json_)
```
npm install --no-bin-links    (for windows installation)
```

7. Copia il file `.env` presente nella cartella `C:/xampp/htdocs/sistema-informativo-nomadelfia/sin`  del server (192.168.11.7) nella cartella `C:/xampp/htdocs/sistema-informativo-nomadelfia/sin` nel computer dove procendeno con l'installazione.
```
   SENTRY_LARAVEL_DSN=https://<key>@o1373805.ingest.sentry.io/6680512
   SENTRY_SEND_DEFAULT_PII=false
   SENTRY_ENVIRONMENT=
```

9. Genera una chiave di sicurezza (N.B: senza la chiave il programma da un errore) che laravel utilizza per crittografare la comunicazione.

```
cd C:/xampp/htdocs/sistema-informativo-nomadelfia/sin

php artisan key:generate
```
9. Create the databases
```
CREATE DATABASE db_admsys  CHARACTER SET = 'utf8'  COLLATE = 'utf8_general_ci';
CREATE DATABASE db_nomadelfia  CHARACTER SET = 'utf8'  COLLATE = 'utf8_general_ci';
CREATE DATABASE archivio_biblioteca  CHARACTER SET = 'utf8'  COLLATE = 'utf8_general_ci';
CREATE DATABASE db_scuola  CHARACTER SET = 'utf8'  COLLATE = 'utf8_general_ci';
```   
10. Execute migrations 
IMPORTANT: do not execute on production

- `php artisan migrate --path="database/migrations/admsys" --database=db_auth`
- `php artisan migrate --path="database/migrations/db_nomadelfia" --database=db_nomadelfia`
- `php artisan migrate --path="database/migrations/biblioteca" --database=db_biblioteca`

11. Seed di dati

```
php artisan db:seed
```

La password di default dell'utente `Admin` per entrare nel pannello di controllo:
 - `Username: Admin`
 - `Password: nomadelfia`

12. Install frontend dependencies
```
npm run prod
```

### Start server
Prima di configurare il server apache, prova ad eseguire il seguente comando per testare se l'installazione è andata a buon fine.

4. Start the **development** Server
```
cd C:/xampp/htdocs/sistema-informativo-nomadelfia/sin
php artisan serve
```

### Configurazione di Apache
Configurazione del web server Apache.
Aprire il file  `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
Aggiungere il seguente Virtual Host
```
<VirtualHost *:80>
   DocumentRoot "C:/xampp/htdocs/sistema-informativo-nomadelfia/sin/public"
   ServerName 127.0.0.1
   ServerAlias 127.0.0.1
   ErrorLog "logs/archivio-error.log"
   CustomLog "logs/archivio-access.log" common
</VirtualHost>
```

Fai ripartire il server apache da xampp.

### Abilitazione OPCache

Abilitare l'estensione `zend_extension=opcache` per diminuire la latenza. 

Queste sono le configurazioni di opcache usate e prese da [qui](https://medium.com/appstract/make-your-laravel-app-fly-with-php-opcache-9948db2a5f93)

```aidl
opcache.enable=1
opcache.memory_consumption=512
opcache.interned_strings_buffer=64
opcache.max_accelerated_files=32531
opcache.validate_timestamps=1
opcache.revalidate_freq=4
opcache.save_comments=1
```

Abilitando l'estensione, il tempo di bootstrap è sceso notevolmente
Da `300ms` a `30ms`.

## Local Dev with Docker
Exploits the Laravel sail (https://laravel.com/docs/9.x/sail) package to run the app into docker containers

- Clone the repo: `git clone git@github.com:noma-labs/sistema-informativo-nomadelfia.git`
- Install the php dep: `cd sin &&  composer install` (inncluding sail script)
- `cp .env.docker .env`
- ./vendor/bin/sail composer run-script post-root-package-install
- ./vendor/bin/sail php artisan key:generate
- ./vendor/bin/sail npm install
- ./vendor/bin/sail npm run production
- `./vendor/bin/sail up`

Create the table structure:
-  `docker-compose exec app php artisan migrate:refresh --path="database/migrations/admsys" --database=db_auth`
-  `docker-compose exec app php artisan migrate:refresh --path="database/migrations/db_nomadelfia" --database=db_nomadelfia`
-  `docker-compose exec app php artisan migrate:refresh --path="database/migrations/biblioteca" --database=db_biblioteca`

Seed the table
-  `docker-compose exec app php artisan db:seed`

## Importazione Database da dump.

L'importazione deve seguire il seguente ordine di importazione dei database:
1.  db_nomadelfia
2.	db_admsys
3.	db_anagrafe
4.	db_patente
5.	db_meccanica
6.	archivio_biblioteca
7.	archivio_nomadelfia

##  Running unit test
IMPORTANT: do not run test on production

-  `.\vendor\bin\phpunit --testdox`
Running tests inside docker   
- `docker-compose exec app php ./vendor/bin/phpunit --testdox `

Create an unit test
- `php artisan make:test UserTest --unit`

Create unit test with docker
- `docker-compose exec app php artisan make:test CaricheTest --unit`
## Struttura ER database

- [cartella drive](https://drive.google.com/open?id=190iYionZjETbbRi_J6G6534Bkx3apkpx)
- [Dia software](http://dia-installer.de/index.html.en): tool utilizzato per disegnare il digaramma ER.

<!-- <p align="center">
<img src="./docs/diagram/Archivio_diagrammaER.png" width="600">
</p> -->

## Database migration
Le migration del database vengono fatte usando il pacchetto  https://github.com/pmatseykanets/laravel-sql-migrations
Questo permette di creare le migration in formato SQL.

Create a migration:
- `php artisan make:migration create_flights_table  --path="database/migrations/db_nomadelfia" --sql`

With sail (docker-compose) 
- ` sail php artisan make:migration create_flights_table  --path="database/migrations/db_nomadelfia" --sql`

  Refresh the structure
- `php artisan migrate:refresh --path="database/migrations/db_nomadelfia" --database=db_nomadelfia`

## Seed dati authentiazione
Per popolare le tabelle dell'authenticazione (ruoli, permessi, utenti) eseguire il comando:
```
php artisan db:seed
```

## Comandi utili

- `composer install`

- `composer dump-autoload`

Error: duplicate entry when auto incremtn a colum. X = max number od the id
- ```ALTER TABLE `classificazione` AUTO_INCREMENT = X, CHANGE `ID_CLASSE` `ID_CLASSE` INT(10) NOT NULL AUTO_INCREMENT```

## Coding convention

### Index name
Use the following structure for the constraints definition:
```
    <prefix>_<table_name>_<column_name>
 ```
The prefix indicates the index type. In MySQL, I prefer:
     idx_ : regular index
     unq_ : UNIQUE
     ftx_ : FULLTEXT
     gis_ : SPATIAL
```

## Front-end

- `npm install --no-bin-links`    (for windows installation)

Run all Mix tasks...
- `npm run dev`

 Run all Mix tasks and minify output...
- `npm run production`

Run all mix Task and look for changes
- `npm run watch

## Troubleshooting 
  - On windows, the html-to-pdf executable throws an error:
    ```
    C:/xampp/htdocs/sistema-informativo-nomadelfia/sin/vendor/wemersonjanuario/wkhtmltopdf-windows/bin/64bit/wkhtmltopdf.exe: 
    error while loading shared libraries: MSVCR120.dll: cannot open shared object file: No such file or directory
    ```
    [solution]: Install the visual studio c++ 2013 [here](https://www.microsoft.com/it-it/download/details.aspx?id=40784)

