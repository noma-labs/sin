<?php

declare(strict_types=1);

use SqlMigrations\SqlMigration;

final class CreatePersoneTable extends SqlMigration
{
    // Use a non default connection
    public $connection = 'db_nomadelfia';
}
