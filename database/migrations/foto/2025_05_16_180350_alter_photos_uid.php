<?php

declare(strict_types=1);

use SqlMigrations\SqlMigration;

final class AlterPhotosUid extends SqlMigration
{
    public $connection = 'db_foto';
}
