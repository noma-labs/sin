<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;

it('syncs mp3 audio rows to recordings by extracted code', function (): void {
    $connection = DB::connection('archivio_nomadelfia');

    $recordingId = $connection->table('recordings')->insertGetId([
        'code' => '194911080M',
        'DATA' => '1949-11-08',
        'ORE' => '0M',
        'LOCALITA' => 'Nomadelfia',
    ]);

    $audioId = $connection->table('recording_audio')->insertGetId([
        'file_name' => '49110800M.mp3',
        'file_path' => '1950/49110800M.mp3',
        'file_size_bytes' => 1024,
        'code' => null,
        'recording_id' => null,
    ]);

    $recordingIdWithSuffix = $connection->table('recordings')->insertGetId([
        'code' => '1976042800',
        'DATA' => '1976-04-28',
        'ORE' => '00',
        'LOCALITA' => 'Nomadelfia',
    ]);

    $audioIdWithSuffix = $connection->table('recording_audio')->insertGetId([
        'file_name' => '76042800 Gioacchino.mp3',
        'file_path' => '1976/76042800 Gioacchino.mp3',
        'file_size_bytes' => 2048,
        'code' => null,
        'recording_id' => null,
    ]);

    $this->artisan('transcripts:sync')->assertSuccessful();

    $audioRow = $connection->table('recording_audio')->where('id', $audioId)->first();
    $audioRowWithSuffix = $connection->table('recording_audio')->where('id', $audioIdWithSuffix)->first();

    expect($audioRow)->not->toBeNull();
    expect($audioRow->code)->toBe('4911080M');
    expect($audioRow->recording_id)->toBe($recordingId);

    expect($audioRowWithSuffix)->not->toBeNull();
    expect($audioRowWithSuffix->code)->toBe('76042800');
    expect($audioRowWithSuffix->recording_id)->toBe($recordingIdWithSuffix);
});
