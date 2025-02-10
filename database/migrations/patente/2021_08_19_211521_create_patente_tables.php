<?php

declare(strict_types=1);

use SqlMigrations\SqlMigration;

final class CreatePatenteTables extends SqlMigration
{
    public $connection = 'db_patente';
}
