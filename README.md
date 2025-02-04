# SIN
The SIN is an Web-based system that permits to manage a series of subsystem (e..g., a school library, a management system for vehicle reservations).

[![Actions Status](https://github.com/noma-labs/sin/workflows/tests/badge.svg)](https://github.com/noma-labs/sin/actions)

## Installation
### Using XAMMP on Windows
**WARNING**: Installing XAMPP will delete all databases and sites in the C:/xampp/htdocs folder. As a precaution, copy the folder before proceeding with the installation of XAMPP.

1. Download and install [`xampp`](https://www.apachefriends.org/it/index.html)  with PHP 8.1.x, MariaDB 10.4.x, PhpMyAdmin: 5.2.0.

2. Download and install  [`Composer`](https://getcomposer.org/download/).

3. Download and install  [`node.js v16.x'](https://nodejs.org/it/download/)

4. Open a shell (e.g., PowerShell or cmd), navigate to the C:/xampp/htdocs folder, and clone the repository.
    ```
    cd  C:/xampp/htdocs
    git clone https://github.com/noma-labs/sin.git
    ```

5. Open the `php.ini` file and enable the PHP extensions (delete the ; character at the beginning of the row): `gd`. and `extension=intl` (required by Number::fileSize)

6. Navigate to the `C:/xampp/htdocs/sin` folder and install the PHP dependencies with Composer (installs the libraries by reading the composer-lock.json file):
    ```
    cd C:/xampp/htdocs/sin
    composer install
    ```

6. Install the frontend dependencies with npm (installs the frontend dependencies by reading the package-lock.json file):
    ```
    npm install --no-bin-links    (for windows installation)
    npm install mix
    npm run production
   ```
7. Copy the `.env-example` file to a new file named `.env` and modify the environment variables as needed.

8. Generate a security key that Laravel uses to encrypt communication:

    ```
    php artisan key:generate
    ```

9.  Create the databases.

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

11. Import the backups (if needed)

12. Configure Apache. OPen the file  `C:\xampp\apache\conf\extra\httpd-vhosts.conf` and add the following virtual host
    ```
    <VirtualHost *:80>
        DocumentRoot "C:/xampp/htdocs/sin/public"
        ServerName 127.0.0.1
        ServerAlias 127.0.0.1
        ErrorLog "logs/sin.log"
        CustomLog "logs/sin.log" common
    </VirtualHost>
    ```
13. Optimizations:
- enable [`OPCache`](https://medium.com/appstract/make-your-laravel-app-fly-with-php-opcache-9948db2a5f93).  First allow the `zend_extension=opcache` extensione and then edit the `php.ini` file.

```ini
opcache.enable=1
opcache.memory_consumption=512
opcache.interned_strings_buffer=64
opcache.max_accelerated_files=32531
opcache.validate_timestamps=1
opcache.revalidate_freq=4
opcache.save_comments=1
```
- Increase max upload size: Open the `php.ini` file and edit the following config: `upload_max_filesize=200M` and `post_max_size=200M`

### Local Dev with Docker
Requirements:
- [Docker](https://docs.docker.com/engine/install/)
- [Taskfile](https://taskfile.dev/)

Steps to run into local macine
- clone the repo `git clone git@github.com:noma-labs/sin.git`
- `task init` to install backend and frontend dependencies and initialize db
- `task up` to start the local development
- open the `http://127.0.0.1:8080/`
- insert username: `admin` password: `admin`

Other commands
- `task test` to execute tests
- `task down` to stop the docker containers
- `task refresh` to seed the databases with dummy data
- `task lint` to run linters (both on php and html/blade)
- `task analyse` to run the static analysis
- create migration `sail php artisan make:migration create_flights_table --path="database/migrations/db_nomadelfia" --sql`
    - index name convention:  `<prefix>_<table_name>_<column_name>` where prefix: `idx_ : regular index`,`unq_ : UNIQUE`
