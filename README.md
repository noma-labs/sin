# Archivio Nomadelfia
## Configurazioni
1. Clone the repository into the  `C:/xampp/htdocs` folder
```
cd  C:/xampp/htdocs
git clone https://github.com/dido18/archivio-nomadelfia.git
```
2. Download and install  [Composer](https://getcomposer.org/download/) (Composer is a tool for dependency management in PHP, install the php libraries)
3. Go into the folder` C:/xampp/htdocs/archivio-nomadelfia/archivio` and install the dependencies   (it installs the libraries dependencies looking at the composer.lock/composer.json file):
```
cd C:/xampp/htdocs/archivio-nomadelfia/archivio
composer install
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
