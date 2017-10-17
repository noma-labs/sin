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

## Create an Admin user

#### Create Admin user and permissions
- Click on Register and create a user (if there "Permission.php not found" error you must delete all the users in the users table)
- then go to http://localhost/permissions. Add permissions "Administer roles & permissions"

- Click on Roles and create these roles
    - Admin: A user assigned to this role would have all permissions
    - Biblioteca: A user assigned to this role would have selected permissions assigned to it by Admin

- Finally assign the Role of 'Admin' to the currently logged in User.

### Commands useful





 php artisan db:seed

## install on windows server

 composer install

 php artisan  migrate --path="database/migrations/auth"
