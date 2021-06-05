# SIN Nomadelfia
SIN (Sistema Informativo Nomadelfia) riunisce tutti i sistemi esistenti in Nomadelfia in un unico sistema.

### Dipendenze

Linguaggi:
   - *php 7.x*
   - *mysql (MariaDB 10.1.x) *
   - *javascript*
   
Framework:
  - backend
      - *Laravel 5.5*
  - frontend
      - *Vue.js 2.x*
      - *Bootstrap  4.x*

## Installazione
**ATTENZIONE**: l'installazione di xampp elimina tutti i database e i siti nella cartella `C:/xampp/htdocs`. Per precauzione copiare la cartella prima di procedere con l'installazione di xampp.

1. Scarica ed installa [`xampp 7.1.10`](https://www.apachefriends.org/it/index.html) (include Apache 2.4.28, MariaDB 10.1.28, PHP 7.1.10, phpMyAdmin 4.7.4).

2. Scarica ed installa [`Composer`](https://getcomposer.org/download/). (Composer 1.8.5 2019-04-09) Composer è un tool a linea di comando che la gestione delle dipendenze PHP.

3. Scarica e instalal [`node.js 8.9.x`](https://nodejs.org/it/download/) (include `npm 5.5.1`)

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

8. Genera una chiave di sicurezza (N.B: senza la chiave il programma da un errore) che laravel utilizza per crittografare la comunicazione.

```
cd C:/xampp/htdocs/sistema-informativo-nomadelfia/sin

php artisan key:generate
```
9. Create the databases
```
CREATE DATABASE db_admsys  CHARACTER SET = 'utf8'  COLLATE = 'utf8_general_ci';
CREATE DATABASE db_nomadelfia  CHARACTER SET = 'utf8'  COLLATE = 'utf8_general_ci';
CREATE DATABASE archivio_biblioteca  CHARACTER SET = 'utf8'  COLLATE = 'utf8_general_ci';
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

Fai ripartire il server  apache da xampp.


## Installazione con Docker

- `docker-compose up`
- `cp .env.example .env`
- `docker-compose exec app composer install`
- `docker-compose exec app php artisan key:generate`
- `docker-compose exec app php artisan config:cache`
  
Create the table structure:
-  ` docker-compose exec app php artisan migrate:refresh --path="database/migrations/admsys" --database=db_auth`
-  `docker-compose exec app php artisan migrate:refresh --path="database/migrations/db_nomadelfia" --database=db_nomadelfia`
-  `docker-compose exec app php artisan migrate:refresh --path="database/migrations/biblioteca" --database=db_biblioteca`

Seed the table
-  `docker-compose exec app php artisan db:seed`

## Importazione Database da dump.

Ogni settimana vengono creati dei dump nell cartella `Z:\sys` sun Nas.
L'importazione deve seguire il seguente ordine di importazione dei database:
1. db_nomadelfia
2.	db_admsys
3.	db_anagrafe
4.	db_patente
5.	db_meccanica
6.	archivio_biblioteca
7.	archivio_nomadelfia

##  Running unit test
IMPORTANT: do not run test on production
- `php artisan make:test UserTest --unit`
-  `.\vendor\bin\phpunit --testdox`

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
- `php artisan make:migration create_flights_table  --path="database/migrations/nomadelfia" --sql`

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

## Front-end

- `npm install --no-bin-links`    (for windows installation)

Run all Mix tasks...
- `npm run dev`

 Run all Mix tasks and minify output...
- `npm run production`

Run all mix Task and look for changes
- `npm run watch`

