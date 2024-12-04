<?php

declare(strict_types=1);

use SqlMigrations\SqlMigration;

final class CreateAziendeTable extends SqlMigration
{
    public $connection = 'db_nomadelfia';
}
