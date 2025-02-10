<?php

declare(strict_types=1);

use SqlMigrations\SqlMigration;

final class CreateDbAdmsysTable extends SqlMigration
{
    public $connection = 'db_auth';
}
