<?php

declare(strict_types=1);

use SqlMigrations\SqlMigration;

final class CreatePhotosTable extends SqlMigration
{
    public $connection = 'db_foto';
}
