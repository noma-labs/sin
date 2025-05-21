<?php

declare(strict_types=1);

use SqlMigrations\SqlMigration;

final class AddPhotoIndexes extends SqlMigration
{
    public $connection = 'db_foto';
}
