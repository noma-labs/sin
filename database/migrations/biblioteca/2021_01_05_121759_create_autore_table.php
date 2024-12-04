<?php

declare(strict_types=1);

use SqlMigrations\SqlMigration;

final class CreateAutoreTable extends SqlMigration
{
    public $connection = 'db_biblioteca';
}
