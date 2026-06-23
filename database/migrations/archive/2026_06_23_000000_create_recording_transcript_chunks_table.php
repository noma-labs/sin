<?php

declare(strict_types=1);

use SqlMigrations\SqlMigration;

final class CreateRecordingTranscriptChunksTable extends SqlMigration
{
    public $connection = 'archivio_nomadelfia';
}
