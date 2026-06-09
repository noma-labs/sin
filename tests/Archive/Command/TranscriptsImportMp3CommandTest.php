<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

beforeEach(function (): void {
    // Create temp test structure: 1950/, 1951/ with MP3 files
    $tempDir = storage_path('app/test_mp3_import');
    if (File::exists($tempDir)) {
        File::deleteDirectory($tempDir);
    }

    File::makeDirectory("{$tempDir}/1950", recursive: true);
    File::makeDirectory("{$tempDir}/1951", recursive: true);

    // Create test MP3 files (dummy files, just for name/path tracking)
    File::put("{$tempDir}/1950/recording_001.mp3", 'test audio content 1');
    File::put("{$tempDir}/1950/recording_002.mp3", 'test audio content 2' . str_repeat('x', 1000)); // ~1KB
    File::put("{$tempDir}/1951/recording_003.mp3", 'test audio content 3' . str_repeat('y', 10000)); // ~10KB

    $this->tempDir = $tempDir;
});

afterEach(function (): void {
    if (isset($this->tempDir) && File::exists($this->tempDir)) {
        File::deleteDirectory($this->tempDir);
    }
});

it('imports mp3 files from year-based folder structure', function (): void {
    $this->artisan('transcripts:import-mp3', ['path' => $this->tempDir])
        ->assertSuccessful();

    $records = DB::connection('archivio_nomadelfia')->table('recording_audio')->get();

    expect($records)->toHaveCount(3);
    expect($records[0]->file_name)->toBe('recording_001.mp3');
    expect($records[0]->file_path)->toContain('1950/recording_001.mp3');
    expect($records[0]->file_size_mb)->toBeFloat();

    expect($records[1]->file_name)->toBe('recording_002.mp3');
    expect($records[2]->file_name)->toBe('recording_003.mp3');
});

it('truncates table when --truncate option is used', function (): void {
    // Pre-populate table
    DB::connection('archivio_nomadelfia')->table('recording_audio')->insert([
        ['file_name' => 'old_file.mp3', 'file_path' => '/path/old.mp3', 'file_size_mb' => 1.5],
    ]);

    expect(DB::connection('archivio_nomadelfia')->table('recording_audio')->count())->toBe(1);

    $this->artisan('transcripts:import-mp3', [
        'path' => $this->tempDir,
        '--truncate' => true,
    ])->assertSuccessful();

    // Old record should be gone, only new imports remain
    $records = DB::connection('archivio_nomadelfia')->table('recording_audio')->get();
    expect($records)->toHaveCount(3);
    expect($records->pluck('file_name'))->not->toContain('old_file.mp3');
});

it('performs dry-run without inserting rows', function (): void {
    $this->artisan('transcripts:import-mp3', [
        'path' => $this->tempDir,
        '--dry-run' => true,
    ])->assertSuccessful();

    $count = DB::connection('archivio_nomadelfia')->table('recording_audio')->count();
    expect($count)->toBe(0);
});

it('fails when directory does not exist', function (): void {
    $this->artisan('transcripts:import-mp3', ['path' => '/nonexistent/path'])
        ->assertFailed();
});

it('handles empty year folders gracefully', function (): void {
    File::makeDirectory("{$this->tempDir}/1952", recursive: true);
    // No MP3 files in 1952

    $this->artisan('transcripts:import-mp3', ['path' => $this->tempDir])
        ->assertSuccessful();

    // Should still import files from other folders
    $records = DB::connection('archivio_nomadelfia')->table('recording_audio')->get();
    expect($records)->toHaveCount(3);
});

it('stores correct file size in megabytes', function (): void {
    $this->artisan('transcripts:import-mp3', ['path' => $this->tempDir])
        ->assertSuccessful();

    $records = DB::connection('archivio_nomadelfia')->table('recording_audio')->orderBy('id')->get();

    expect($records[0]->file_size_mb)->toBeGreaterThan(0);
    expect($records[0]->file_size_mb)->toBeLessThan(1); // Small test files
});
