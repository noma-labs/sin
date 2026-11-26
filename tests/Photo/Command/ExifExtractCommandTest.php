<?php

declare(strict_types=1);

use App\Photo\Models\ExifData;
use Illuminate\Support\Facades\File;

it('extracts exif info into a json  with a folder', function (): void {
    $this->artisan('exif:extract', ['path' => 'tests/Photo/Command/testfile'])->assertSuccessful();
});

it('extract exif into a json file with a folder with spaces', function (): void {
    $this->artisan('exif:extract', ['path' => 'tests/Photo/Command/testfile/2025-09-19 AAA A test folder'])->assertSuccessful();
    expect(File::exists('tests/Photo/Command/testfile/2025-09-19 AAA A test folder/2025-09-19 AAA A test folder.json'))->toBeTrue();
});

it('extracts exif info into a json  with a file', function (): void {
    $this->artisan('exif:extract', ['path' => 'tests/Photo/Command/testfile/BlueSquare.jpg'])->assertSuccessful();
});

it('runs the exiftool binary configured with EXIFTOOL_PATH', function (): void {
    $folder = storage_path('framework/testing/exif-binary');
    File::ensureDirectoryExists($folder);
    File::copy(base_path('tests/Photo/Command/testfile/BlueSquare.jpg'), $folder.'/BlueSquare.jpg');
    config(['services.exiftool.path' => '/missing/exiftool']);

    $this->artisan('exif:extract', ['path' => $folder])->assertSuccessful();

    expect(File::get($folder.'/exif-binary.json'))->toBe('');

    File::deleteDirectory($folder);
});

it('runs the exiftool binary given with --exiftoolpath instead of the configured one', function (): void {
    $folder = storage_path('framework/testing/exif-binary-option');
    File::ensureDirectoryExists($folder);
    File::copy(base_path('tests/Photo/Command/testfile/BlueSquare.jpg'), $folder.'/BlueSquare.jpg');
    config(['services.exiftool.path' => '/missing/exiftool']);

    $this->artisan('exif:extract', ['path' => $folder, '--exiftoolpath' => 'exiftool'])->assertSuccessful();

    expect(File::get($folder.'/exif-binary-option.json'))->toContain('BlueSquare.jpg');

    File::deleteDirectory($folder);
});

it('extracts only the tags read by the import, with the same image hash', function (): void {
    $folder = storage_path('framework/testing/exif-tags');
    File::ensureDirectoryExists($folder);
    File::copy(base_path('tests/Photo/Command/testfile/BlueSquare.jpg'), $folder.'/BlueSquare.jpg');

    $this->artisan('exif:extract', ['path' => $folder])->assertSuccessful();

    $photo = collect(json_decode(File::get($folder.'/exif-tags.json'), true))
        ->firstWhere('System:FileName', 'BlueSquare.jpg');
    expect(array_keys($photo))->each->toBeIn(['SourceFile', ...ExifData::EXTRACTED_TAGS])
        ->and($photo['File:ImageDataHash'])->toBe('759f64681d007051dd64e1a4f9586453');

    File::deleteDirectory($folder);
});
