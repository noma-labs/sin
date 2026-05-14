<?php

declare(strict_types=1);

use SqlMigrations\SqlMigration;

final class CreateAudioTranscriptTable extends SqlMigration
{
    public $connection = 'archivio_documenti';
}
