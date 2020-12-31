<?php

use SqlMigrations\SqlMigration;

class CreateDbAdmsysTable extends SqlMigration
{
        // Use a non default connection
        public $connection = 'db_auth';
}
