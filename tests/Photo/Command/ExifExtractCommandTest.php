<?php

declare(strict_types=1);

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
