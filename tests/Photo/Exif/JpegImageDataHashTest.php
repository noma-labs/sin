<?php

declare(strict_types=1);

use App\Photo\Exif\JpegImageDataHash;
use Illuminate\Support\Facades\File;

/**
 * SOI, an APP0 and a SOF0 segment: metadata that is never hashed.
 */
function jpegHeader(): string
{
    return "\xFF\xD8"."\xFF\xE0\x00\x04ab"."\xFF\xC0\x00\x0B".str_repeat('s', 9);
}

function hashOfJpegBytes(string $bytes): ?string
{
    $file = tempnam(sys_get_temp_dir(), 'jpeg');
    file_put_contents($file, $bytes);

    try {
        return JpegImageDataHash::ofFile($file);
    } finally {
        File::delete($file);
    }
}

it('returns the same hash as exiftool for a photo', function (): void {
    expect(JpegImageDataHash::ofFile(__DIR__.'/../Command/testfile/BlueSquare.jpg'))
        ->toBe('759f64681d007051dd64e1a4f9586453');
});

// The expected values were checked against exiftool 13.59 -ImageDataHash.
it('hashes the image data like exiftool', function (string $jpeg, string $hashedBytes): void {
    expect(hashOfJpegBytes($jpeg))->toBe(md5($hashedBytes));
})->with([
    'from the start of scan to the end of image, with fill bytes, restarts and a trailer' => [
        jpegHeader()."\xFF\xDA\x00\x04xy"."abc\xFF\x00def\xFF\xFF\x00gh\xFF\xD0ij"."\xFF\xFF\xD9"."trailer\xFF\xD9zz",
        "\xFF\xDA\x00\x04xyabc\xFF\x00def\xFF\xFF\x00gh\xFF\xD0ij\xFF",
    ],
    'without the data before a segment between the scans of a progressive jpeg' => [
        jpegHeader()."\xFF\xDA\x00\x04xy"."abc\xFF\x00def"."\xFF\xC4\x00\x04qq"."\xFF\xDA\x00\x04zw".'ghi'."\xFF\xD9",
        "\xFF\xDA\x00\x04xyabc"."\xFF\xDA\x00\x04zwghi",
    ],
    'without the last piece of data of a truncated file ending with 0xFF' => [
        jpegHeader()."\xFF\xDA\x00\x04xy"."abc\xFF\x00def\xFF",
        "\xFF\xDA\x00\x04xyabc",
    ],
]);

it('returns no hash when exiftool returns none', function (string $bytes): void {
    expect(hashOfJpegBytes($bytes))->toBeNull();
})->with([
    'a jpeg without image data' => [jpegHeader()."\xFF\xD9"],
    'a file that is not a jpeg' => ["\x89PNG\r\n\x1a\n".str_repeat('p', 40)],
    'a file starting with FF D8 but not FF D8 FF' => ["\xFF\xD8\x00\xFF\xDA\x00\x04xyabc\xFF\xD9"],
]);

it('returns no hash for a missing file', function (): void {
    expect(JpegImageDataHash::ofFile(__DIR__.'/missing.jpg'))->toBeNull();
});
