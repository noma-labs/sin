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
    - *Laravel 10.x* (10.48.9)
- frontend
    - *Vue.js 2.x*
    - *Bootstrap 4.x*

Other

- *PhpMyAdmin : 5.2.0*
- *npm 8.15.X*
- *composer 2.4.x*

Required PHP extensions

- gd
- zip (require apt-get install libzip-dev, zip) -> required by spreedsheet
- exif -> required by spatie/media-library
- pdo_mysql
- mbstring -> required by laravel sail

3rd party services

- Email: postmarkapp

## Installazione in produzione

**ATTENZIONE**: l'installazione di xampp elimina tutti i database e i siti nella cartella `C:/xampp/htdocs`. Per
precauzione copiare la cartella prima di procedere con l'installazione di xampp.

1. Scarica ed installa [`xampp`](https://www.apachefriends.org/it/index.html)

2. Scarica ed installa [`Composer`](https://getcomposer.org/download/). Composer è un tool a linea di comando che la
   gestione delle dipendenze PHP.

3. Scarica e install [`node.js`](https://nodejs.org/it/download/)

5. Apri una shell (e.g. PowerShell o cmd) entra nella cartella `C:/xampp/htdocs`  e scarica la repository.
    ```
    cd  C:/xampp/htdocs
    git clone https://github.com/noma-labs/sin.git
    ```

4.1. Open the `php.ini` file and enable the php extensions (delete the ; char at the begin of the row): `gd`

5. Entra nella cartella ` C:/xampp/htdocs/sin` e installa le dipendeze php con `composer` (installa le librerie leggendo
   il file _composer-lock.json_):
    ```
    cd C:/xampp/htdocs/sin
    composer install
    ```

6. Installa le dipendenze con `npm` (installa le dipendenze front end leggendo il file _packages-lock.json_)
    ```
    npm install --no-bin-links    (for windows installation)
    ```
7. Install frontend dependencies
   ```
    npm install mix
    npm run production
   ```
8. Copia il file `.env-example`in un file `.env ` e modifica le variabili d'ambiente

9. Genera una chiave di sicurezza che laravel utilizza per crittografare la comunicazione.

    ```
    php artisan key:generate
    ```

10. Create the databases.

  ```
CREATE DATABASE IF NOT EXISTS  archivio_biblioteca;
CREATE DATABASE IF NOT EXISTS  archivio_nomadelfia;
CREATE DATABASE IF NOT EXISTS  db_admsys;
CREATE DATABASE IF NOT EXISTS  db_agraria;
CREATE DATABASE IF NOT EXISTS  db_foto;
CREATE DATABASE IF NOT EXISTS  db_meccanica;
CREATE DATABASE IF NOT EXISTS  db_nomadelfia;
CREATE DATABASE IF NOT EXISTS  db_noma_iot;
CREATE DATABASE IF NOT EXISTS  db_patente;
CREATE DATABASE IF NOT EXISTS  db_scuola;
```

11. Import the dumps

### Start server

Prima di configurare il server apache, prova ad eseguire il seguente comando per testare se l'installazione è andata a
buon fine.

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

Queste sono le configurazioni di opcache usate e prese
da [qui](https://medium.com/appstract/make-your-laravel-app-fly-with-php-opcache-9948db2a5f93)

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

Prerequisiti:
- [Docker](https://docs.docker.com/engine/install/)
- [Taskfile](https://taskfile.dev/)


Installazione in locale
- clone the repo `git clone git@github.com:noma-labs/sin.git`
- `task init` to install backend and frontend dependencies and initialize db
- `task up` to start the local development
- open the `http://127.0.0.1:8080/`
- insert username: `admin` password: `admin`

Altri comandi utili
- `task test` to execute tests
- `task down` to stop the docker contaneis
- `task refresh` to seed the databases with dummy data
- `task lint` to run linters (both on php and html/blade)
- `tak analyse` to run the static analysis


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
- Create migration with [sql](https://github.com/pmatseykanets/laravel-sql-migrations)  ` sail php artisan make:migration create_flights_table --path="database/migrations/db_nomadelfia" --sql`
- Error: duplicate entry when auto incrementing a column. where `X` = max number of the id
  ```ALTER TABLE `classificazione` AUTO_INCREMENT = X, CHANGE `ID_CLASSE` `ID_CLASSE` INT(10) NOT NULL AUTO_INCREMENT```
