<?php

declare(strict_types=1);

use SqlMigrations\SqlMigration;

final class CreateRtnTables extends SqlMigration
{
    public $connection = 'db_rtn';
}
