<?php

declare(strict_types=1);

use App\Console\Commands\TranscriptsImportDocxCommand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

it('restores the fulltext index after import command execution', function (): void {
    Storage::fake('transcripts_originals');

    $connection = DB::connection('archivio_nomadelfia');
    $command = new TranscriptsImportDocxCommand();

    $dropIndex = new ReflectionMethod(TranscriptsImportDocxCommand::class, 'dropFullTextIndexIfExists');
    $dropIndex->setAccessible(true);
    $indexExists = new ReflectionMethod(TranscriptsImportDocxCommand::class, 'fullTextIndexExists');
    $indexExists->setAccessible(true);

    $dropIndex->invoke($command, $connection);
    expect($indexExists->invoke($command, $connection))->toBeFalse();

    $this->artisan('transcripts:import-docx')->assertFailed();

    expect($indexExists->invoke($command, $connection))->toBeTrue();
});
