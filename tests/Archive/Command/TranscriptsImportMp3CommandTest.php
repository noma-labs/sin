<?php

declare(strict_types=1);

use App\Console\Commands\TranscriptsImportMp3Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Storage::fake('audio_originals');
    Storage::disk('audio_originals')->put('1950/49110800A.mp3', 'test audio content 1');
    Storage::disk('audio_originals')->put('1950/50121600.mp3', 'test audio content 2'.str_repeat('x', 1000));
    Storage::disk('audio_originals')->put('1950/4912070A MY AUDIO.mp3', 'test audio content 3');
});

it('imports mp3 files from audio_originals disk', function (): void {
    $this->artisan('transcripts:import-mp3')->assertSuccessful();

    $records = DB::connection('archivio_nomadelfia')
        ->table('recording_audio')
        ->orderBy('file_name')
        ->get();

    expect($records)->toHaveCount(3);
    expect($records[0]->file_name)->toBe('49110800A.mp3');
    expect($records[0]->code)->toBe('49110800A');
    expect($records[0]->file_path)->toContain('1950/49110800A.mp3');
    expect($records[0]->file_size_bytes)->toBeInt();
    expect($records[0]->file_size_bytes)->toBeGreaterThan(0);

    expect($records[1]->file_name)->toBe('4912070A MY AUDIO.mp3');
    expect($records[1]->code)->toBe('4912070A');
    expect($records[1]->file_path)->toContain('1950/4912070A MY AUDIO.mp3');
    expect($records[1]->file_size_bytes)->toBeInt();

    expect($records[2]->file_name)->toBe('50121600.mp3');
    expect($records[2]->code)->toBe('50121600');
    expect($records[2]->file_path)->toContain('1950/50121600.mp3');
    expect($records[2]->file_size_bytes)->toBeInt();
});

it('builds code from file name', function (string $fileName, string $expectedCode): void {
    $command = new TranscriptsImportMp3Command;

    $buildCodeMethod = new ReflectionMethod(TranscriptsImportMp3Command::class, 'buildCode');
    $buildCodeMethod->setAccessible(true);

    $code = $buildCodeMethod->invoke($command, $fileName);

    expect($code)->toBe($expectedCode);
})->with([
    'plain code filename' => ['49110800A.mp3', '49110800A'],
    'filename with title' => ['4912070A MY BEST MOVIE.mp3', '4912070A'],
    'uppercase extension' => ['50121600.MP3', '50121600'],
    'leading spaces before code' => [' 50120900 WITH SPACE.mp3', '50120900'],
]);
