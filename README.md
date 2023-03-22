# SIN Nomadelfia
SIN (Sistema Informativo Nomadelfia) riunisce tutti i sistemi esistenti in Nomadelfia in un unico sistema.

[![Actions Status](https://github.com/noma-labs/sin/workflows/tests/badge.svg)](https://github.com/noma-labs/sin/actions)

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
    git clone https://github.com/noma-labs/sistema-informativo-nomadelfia.git
    ```

5. Entra nella cartella ` C:/xampp/htdocs/sistema-informativo-nomadelfia` e installa le dipendeze php con `composer` (installa le librerie leggendo il file _composer-lock.json_):
    ```
    cd C:/xampp/htdocs/sistema-informativo-nomadelfia
    composer install
    ```

6. Installa le dipendenze con `npm` (installa le dipendenze  front end leggendo il file _packages-lock.json_)
    ```
    npm install --no-bin-links    (for windows installation)
    ```

7. Copia il file `.env-example`in un file `.env ` e modifica le variabili d'ambiente

8. Genera una chiave di sicurezza che laravel utilizza per crittografare la comunicazione.
    
    ```
    cd C:/xampp/htdocs/sistema-informativo-nomadelfia
    
    php artisan key:generate
    ```

9. Importa il dump dei database

10. Install frontend dependencies
```
npm run prod
```

### Start server
Prima di configurare il server apache, prova ad eseguire il seguente comando per testare se l'installazione è andata a buon fine.

Start the **development** Server
```
cd C:/xampp/htdocs/sistema-informativo-nomadelfia
php artisan serve
```

### Configurazione di Apache
Configurazione del web server Apache.
Aprire il file  `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
Aggiungere il seguente Virtual Host
```
<VirtualHost *:80>
   DocumentRoot "C:/xampp/htdocs/sistema-informativo-nomadelfia/public"
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
- Init the local dev: `./init.sh`
- Start the containers`./start.sh`
- Refresh and seed the database `./refresh.sh`
- Run unit tests `./test.sh`

- open http://127.0.0.1:8080/. La password di default dell'utente `Admin` per entrare nel pannello di controllo:
  - `Username: admin`
  - `Password: admin`

## Importazione Database da dump.

L'importazione deve seguire il seguente ordine di importazione dei database:
1.  db_nomadelfia
2.	db_admsys
3.	db_anagrafe
4.	db_patente
5.	db_meccanica
6.	archivio_biblioteca
7.	archivio_nomadelfia


## Database migration
Le migration del database vengono fatte usando il pacchetto  https://github.com/pmatseykanets/laravel-sql-migrations
Questo permette di creare le migration in formato SQL.

Create a migration:
- `php artisan make:migration create_flights_table  --path="database/migrations/db_nomadelfia" --sql`

With sail (docker-compose) 
- ` sail php artisan make:migration create_flights_table  --path="database/migrations/db_nomadelfia" --sql`

Refresh the structure
- `php artisan migrate:refresh --path="database/migrations/db_nomadelfia" --database=db_nomadelfia`

## Coding convention

### Index name
Use the following structure for the constraints definition:
```
    <prefix>_<table_name>_<column_name>

Where, the prefix indicates the index type:
     idx_ : regular index
     unq_ : UNIQUE
     ftx_ : FULLTEXT
     gis_ : SPATIAL
```

## Troubleshooting 
  - On windows, if the html-to-pdf executable throws an error:
    ```
    C:/xampp/htdocs/sistema-informativo-nomadelfia/sin/vendor/wemersonjanuario/wkhtmltopdf-windows/bin/64bit/wkhtmltopdf.exe: 
    error while loading shared libraries: MSVCR120.dll: cannot open shared object file: No such file or directory
    ```
    [solution]: Install the visual studio c++ 2013 [here](https://www.microsoft.com/it-it/download/details.aspx?id=40784)
  - Error: duplicate entry when auto incrementing a column. where `X` = max number of the id
 ```ALTER TABLE `classificazione` AUTO_INCREMENT = X, CHANGE `ID_CLASSE` `ID_CLASSE` INT(10) NOT NULL AUTO_INCREMENT```


