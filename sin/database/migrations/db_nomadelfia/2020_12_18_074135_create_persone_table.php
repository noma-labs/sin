<?php

use SqlMigrations\SqlMigration;

class CreatePersoneTable extends SqlMigration
{
    // Use a non default connection
    public $connection = 'db_nomadelfia_test';
}
