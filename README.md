# Archivio Nomadelfia
Archivio Nomadelfia è il sistema informativo informatico di Nomadelfia.

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

2. Scarica ed installa [`Composer`](https://getcomposer.org/download/). Composer è un tool a linea di comando che la gestione delle dipendenze PHP.

3. Scarica e instalal [`node.js 8.9.x`](https://nodejs.org/it/download/) (include `npm 5.5.1`)

4. Apri una shell (e.g. PowerShell o cmd) entra nella cartella `C:/xampp/htdocs`  e scarica la repository.
```
cd  C:/xampp/htdocs
git clone https://github.com/dido18/archivio-nomadelfia.git
```

5. Entra nella cartella ` C:/xampp/htdocs/archivio-nomadelfia/archivio` e installa le dipendeze php con `composer` (installa le librerie leggendo il file _composer.json_):
```
cd C:/xampp/htdocs/archivio-nomadelfia/archivio
composer install
```

6. Installa le dipendenze con `npm` (installa le dipendenze latao front end leggendo il file _packages.json_)
```
npm install --no-bin-links    (for windows installation)
```

7. Copia il file `.env` presente nella cartella `C:/xampp/htdocs/archivio-nomadelfia/archivio`  del server (192.168.11.7) nella cartella `C:/xampp/htdocs/archivio-nomadelfia/archivio` nel computer dove procendeno con l'installazione.

8. Genera una chiave di sicurezza (N.B: senza la chiave il programma da un errore) che laravel utilizza per crittografare la comunicazione.

```
cd C:/xampp/htdocs/archivio-nomadelfia/archivio

php artisan key:generate
```

Se nel database `db_anagrafe` le tabelle `permissions`,  `roles`, `users` sono vuote eseguire il seguent comando per aggiungere nel database i *ruoli*: `biblioteca`, `rtn` `Admin`;  l'*utente*: `Admin` e i *permessi*.

```
cd C:/xampp/htdocs/archivio-nomadelfia/archivio
php artisan db:seed
```
La password di default dell'utente `Admin` per entrare nel pannello di controllo:
 - `Username: Admin`
 - `Password: nomadelfia`

### Test
Prima di configurare il server apache, prova ad eseguire il seguente comando per testare se l'installazione è andata a buon fine.

4. Start the **development** Server
```
cd C:/xampp/htdocs/archivio-nomadelfia/archivio
php artisan serve
```

### Configurazione di Apache
Configurazione del web server Apache.
Aprire il file  `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
Aggiungere il seguente Virtual Host
```
<VirtualHost *:80>
   DocumentRoot "C:/xampp/htdocs/archivio-nomadelfia/archivio/public"
   ServerName 127.0.0.1
   ServerAlias 127.0.0.1
   ErrorLog "logs/archivio-error.log"
   CustomLog "logs/archivio-access.log" common
</VirtualHost>
```

Fai ripartire il server  apache da xampp.

## Struttura ER database

- [cartella drive](https://drive.google.com/open?id=190iYionZjETbbRi_J6G6534Bkx3apkpx)
- [Dia software](http://dia-installer.de/index.html.en): tool utilizzato per disegnare il digaramma ER.

<!-- <p align="center">
<img src="./docs/diagram/Archivio_diagrammaER.png" width="600">
</p> -->

## Seed dati authentiazione
Per popolare le tabelle dell'authenticazione (ruoli, permessi, utenti) eseguire il comando:
```
php artisan db:seed
```

## Comandi utili

- `composer install`

- `php artisan  migrate --path="database/migrations/auth"`

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

##
- https://foundation.zurb.com/ 
