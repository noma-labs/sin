<?php

declare(strict_types=1);

use SqlMigrations\SqlMigration;

final class CreateLibroTable extends SqlMigration
{
    public $connection = 'db_biblioteca';
}
