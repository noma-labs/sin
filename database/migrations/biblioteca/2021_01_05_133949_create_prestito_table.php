<?php

declare(strict_types=1);

use SqlMigrations\SqlMigration;

final class CreatePrestitoTable extends SqlMigration
{
    public $connection = 'db_biblioteca';
}
