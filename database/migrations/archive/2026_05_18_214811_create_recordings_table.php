<?php

declare(strict_types=1);

use SqlMigrations\SqlMigration;

final class CreateRecordingsTable extends SqlMigration
{
    public $connection = 'archivio_nomadelfia';
}
