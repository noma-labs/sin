# Archivio Nomadelfia

### Dipendenze
   - *Laravel 5.5*
   - *Vue.js 2.x*

## Installazione
**ATTENZIONE**: l'installazione di xampp elimina tutti i database e i siti nella cartella `C:/xampp/htdocs`.
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
npm install
```


4. Start the **development** Server
```
php artisan serve
```
Copy `.env` file from the nas to the /archivio-noamdelfia/archivio folder
```
php artisan key:generate
```
### Apache
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
### Gestione archivi
1. Rtn (archivio_tv)
- Archivio Professionale
- Archivio Dvd
2. Biblioteca (archivio_biblioteca)
- Libri
- Video
3. Anagrafe (archivio_anagrafe)


## Set-up

- composer install

- php artisan  migrate --path="database/migrations/auth"

- php artisan db:seed

 Username: Admin

 password: nomadelfia

## Front-end

- Install node.js (from the site)

- npm install --no-bin-links    (for windows installation)

<!-- - npm install --global gulp-cli

Elixir defined gulp tasks

- npm link gulp (if problem with windows) -->
Webpack instead of gulp (from  laravel 5.4)

[mix](https://laravel.com/docs/5.5/mix)

// Run all Mix tasks...
- npm run dev

// Run all Mix tasks and minify output...
- npm run production

// run all mix Task and look for changes
- npm run watch

## Query doe urse to role

CREATE VIEW `archivio_biblioteca`.`users_to_roles` AS SELECT
users.id AS id_user , users.name, users.username, users.email , roles.name AS role FROM archivio_auth.users, archivio_auth.user_has_roles, archivio_auth.roles WHERE archivio_auth.users.id = archivio_auth.user_has_roles.user_id and archivio_auth.roles.id = archivio_auth.user_has_roles.role_id
