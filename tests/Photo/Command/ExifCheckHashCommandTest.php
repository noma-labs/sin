<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

/**
 * @param  array<int, array{SourceFile: string, "File:ImageDataHash"?: string}>  $photos
 */
function exiftoolJson(array $photos): string
{
    $file = storage_path('framework/testing/check-hash.json');
    File::ensureDirectoryExists(dirname($file));
    File::put($file, json_encode($photos));

    return $file;
}

afterEach(function (): void {
    File::delete(storage_path('framework/testing/check-hash.json'));
});

it('succeeds when the php hash of every photo is the one of exiftool', function (): void {
    $json = exiftoolJson([
        ['SourceFile' => base_path('tests/Photo/Command/testfile/BlueSquare.jpg'), 'File:ImageDataHash' => '759f64681d007051dd64e1a4f9586453'],
        ['SourceFile' => base_path('tests/Photo/Command/testfile/photos.json')],
    ]);

    $this->artisan('exif:check-hash', ['file' => $json])
        ->doesntExpectOutputToContain('BlueSquare.jpg')
        ->assertSuccessful();
});

it('fails and lists the photos whose php hash differs from the one of exiftool', function (): void {
    $photo = base_path('tests/Photo/Command/testfile/BlueSquare.jpg');
    $json = exiftoolJson([['SourceFile' => $photo, 'File:ImageDataHash' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa']]);

    $this->artisan('exif:check-hash', ['file' => $json])
        ->expectsOutputToContain($photo)
        ->assertFailed();
});

it('lists the photos that are not found without failing', function (): void {
    $json = exiftoolJson([['SourceFile' => '/missing/photo.jpg', 'File:ImageDataHash' => '759f64681d007051dd64e1a4f9586453']]);

    $this->artisan('exif:check-hash', ['file' => $json])
        ->expectsOutputToContain('/missing/photo.jpg')
        ->assertSuccessful();
});
