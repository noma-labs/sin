<?php

declare(strict_types=1);

use SqlMigrations\SqlMigration;

final class AddScuolaReferenceToPersone extends SqlMigration
{
    public $connection = 'db_scuola';
}
