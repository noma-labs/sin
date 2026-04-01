<?php

declare(strict_types=1);

use SqlMigrations\SqlMigration;

final class CreatePhotosIssuesTable extends SqlMigration
{
    public $connection = 'db_foto';
}
