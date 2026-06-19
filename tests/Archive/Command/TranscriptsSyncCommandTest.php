<?php

declare(strict_types=1);

use App\Console\Commands\TranscriptsSyncCommand;
use Illuminate\Support\Facades\DB;

it('syncs mp3 audio rows to recordings by extracted code', function (): void {
    $connection = DB::connection('archivio_nomadelfia');

    $recordingId = $connection->table('recordings')->insertGetId(
        [
            'DATA' => '1949-11-08',
            'ORE' => '12',
        ],
    );

    $audioId = $connection->table('recording_audio')->insertGetId([
        'file_name' => '49110812.mp3',
        'file_path' => '49/49110812.mp3',
        'file_size_bytes' => 0,
    ]);

    $transcriptID = $connection->table('recording_transcripts')->insertGetId([
        'heading' => '49110812 Transcript Heading',
        'file_path' => '49/49110812.docx',
    ]);

    $this->artisan('transcripts:sync')->assertSuccessful();

    $audioRecordingId = $connection->table('recording_audio')
        ->where('id', $audioId)
        ->value('recording_id');

    $transcriptRecordingId = $connection->table('recording_transcripts')
        ->where('id', $transcriptID)
        ->value('recording_id');

    expect($audioRecordingId)
        ->not->toBeNull()
        ->toBe($recordingId);

    expect($transcriptRecordingId)
        ->not->toBeNull()
        ->toBe($recordingId);
});

it('syncs rows when ORE is null and maps to 0A code', function (): void {
    $connection = DB::connection('archivio_nomadelfia');

    $recordingId = $connection->table('recordings')->insertGetId([
        'DATA' => '1950-12-16',
        'ORE' => null,
    ]);

    $audioId = $connection->table('recording_audio')->insertGetId([
        'file_name' => '501216.mp3',
        'file_path' => '50/501216.mp3',
        'file_size_bytes' => 0,
    ]);

    $transcriptID = $connection->table('recording_transcripts')->insertGetId([
        'heading' => '501216 Transcript Heading',
        'file_path' => '50/501216.docx',
    ]);

    $this->artisan('transcripts:sync')->assertSuccessful();

    $audioRecordingId = $connection->table('recording_audio')
        ->where('id', $audioId)
        ->value('recording_id');

    $transcriptRecordingId = $connection->table('recording_transcripts')
        ->where('id', $transcriptID)
        ->value('recording_id');

    expect($audioRecordingId)
        ->not->toBeNull()
        ->toBe($recordingId);

    expect($transcriptRecordingId)
        ->not->toBeNull()
        ->toBe($recordingId);
});

it('syncs rows when ORE is already 0A', function (): void {
    $connection = DB::connection('archivio_nomadelfia');

    $recordingId = $connection->table('recordings')->insertGetId([
        'DATA' => '1949-12-07',
        'ORE' => '0A',
    ]);

    $audioId = $connection->table('recording_audio')->insertGetId([
        'file_name' => '4912070A.mp3',
        'file_path' => '49/4912070A.mp3',
        'file_size_bytes' => 0,
    ]);

    $transcriptID = $connection->table('recording_transcripts')->insertGetId([
        'heading' => '4912070A Transcript Heading',
        'file_path' => '49/4912070A.docx',
    ]);

    $this->artisan('transcripts:sync')->assertSuccessful();

    $audioRecordingId = $connection->table('recording_audio')
        ->where('id', $audioId)
        ->value('recording_id');

    $transcriptRecordingId = $connection->table('recording_transcripts')
        ->where('id', $transcriptID)
        ->value('recording_id');

    expect($audioRecordingId)
        ->not->toBeNull()
        ->toBe($recordingId);

    expect($transcriptRecordingId)
        ->not->toBeNull()
        ->toBe($recordingId);
});

it('extracts audio code from mp3 file name', function (string $fileName, string $expectedCode): void {
    $command = new TranscriptsSyncCommand();

    $method = new ReflectionMethod(TranscriptsSyncCommand::class, 'extractAudioCodeFromFileName');
    $method->setAccessible(true);

    expect($method->invoke($command, $fileName))->toBe($expectedCode);
})->with([
    'with standard file name' => ['49110812.mp3', '49110812'],
    'with 0A suffix hour' => ['4911080A.MP3', '4911080A'],
    'with non-mp3 extension' => ['4911080A.wav', '4911080A'],
    'with additional text in file name' => ['4911080A MY AUDIO.mp3', '4911080A'],
    'with spaces' => ['   4911080B .mp3', '4911080B'],
])->only();
