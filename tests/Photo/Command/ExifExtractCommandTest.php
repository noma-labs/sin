<?php

declare(strict_types=1);

use App\Photo\Models\ExifData;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Storage::fake('local');
});

it('extracts exif info into a json  with a folder', function (): void {
    $this->artisan('exif:extract', ['path' => 'tests/Photo/Command/testfile'])->assertSuccessful();
});

it('writes the json into storage/app/exif and not inside the scanned folder', function (): void {
    $folder = 'tests/Photo/Command/testfile/2025-09-19 AAA A test folder';
    $jsonFile = Storage::disk('local')->path('exif/2025-09-19 AAA A test folder.json');

    $this->artisan('exif:extract', ['path' => $folder])
        ->expectsOutput("Saved into $jsonFile")
        ->assertSuccessful();

    $photos = json_decode(Storage::disk('local')->get('exif/2025-09-19 AAA A test folder.json'), true);
    expect(array_column($photos, 'System:FileName'))->toBe(['BlueSquareInDir.jpg'])
        ->and(File::glob(base_path($folder).'/*.json'))->toBe([]);
});

it('extracts exif info into a json  with a file', function (): void {
    $this->artisan('exif:extract', ['path' => 'tests/Photo/Command/testfile/BlueSquare.jpg'])->assertSuccessful();

    expect(Storage::disk('local')->get('exif/BlueSquare.jpg.json'))->toContain('BlueSquare.jpg');
});

it('runs the exiftool binary configured with EXIFTOOL_PATH', function (): void {
    config(['services.exiftool.path' => '/missing/exiftool']);

    $this->artisan('exif:extract', ['path' => 'tests/Photo/Command/testfile/BlueSquare.jpg'])->assertSuccessful();

    expect(Storage::disk('local')->get('exif/BlueSquare.jpg.json'))->toBe('');
});

it('runs the exiftool binary given with --exiftoolpath instead of the configured one', function (): void {
    config(['services.exiftool.path' => '/missing/exiftool']);

    $this->artisan('exif:extract', ['path' => 'tests/Photo/Command/testfile/BlueSquare.jpg', '--exiftoolpath' => 'exiftool'])
        ->assertSuccessful();

    expect(Storage::disk('local')->get('exif/BlueSquare.jpg.json'))->toContain('BlueSquare.jpg');
});

it('extracts only the tags read by the import, with the same image hash', function (): void {
    $this->artisan('exif:extract', ['path' => 'tests/Photo/Command/testfile/BlueSquare.jpg'])->assertSuccessful();

    $photos = json_decode(Storage::disk('local')->get('exif/BlueSquare.jpg.json'), true);
    expect($photos)->toHaveCount(1)
        ->and(array_keys($photos[0]))->each->toBeIn(['SourceFile', ...ExifData::EXTRACTED_TAGS])
        ->and($photos[0]['File:ImageDataHash'])->toBe('759f64681d007051dd64e1a4f9586453');
});
