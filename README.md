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

## Add  laravel snappy
https://github.com/barryvdh/laravel-snappy


install onwindows 
https://github.com/barryvdh/laravel-snappy/issues/60
