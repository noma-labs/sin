<?php

declare(strict_types=1);

use SqlMigrations\SqlMigration;

final class CreateOfficinaTables extends SqlMigration
{
    public $connection = 'db_officina';
}
