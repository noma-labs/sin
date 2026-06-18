<?php

declare(strict_types=1);

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
    expect($records[0]->code)->toBeNull();
    expect($records[0]->file_path)->toContain('1950/49110800A.mp3');
    expect($records[0]->file_size_bytes)->toBeInt();
    expect($records[0]->file_size_bytes)->toBeGreaterThan(0);

    expect($records[1]->file_name)->toBe('4912070A MY AUDIO.mp3');
    expect($records[1]->code)->toBeNull();
    expect($records[1]->file_path)->toContain('1950/4912070A MY AUDIO.mp3');
    expect($records[1]->file_size_bytes)->toBeInt();

    expect($records[2]->file_name)->toBe('50121600.mp3');
    expect($records[2]->code)->toBeNull();
    expect($records[2]->file_path)->toContain('1950/50121600.mp3');
    expect($records[2]->file_size_bytes)->toBeInt();
});
