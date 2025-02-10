<?php

declare(strict_types=1);

use SqlMigrations\SqlMigration;

final class CreateScuolaTable extends SqlMigration
{
    public $connection = 'db_scuola';
}
