<?php


use Domain\Photo\Exif\ExifReader;
use Domain\Photo\Models\Photo;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $tempDirPath = __DIR__ . '/temp';
    $this->emptyTempDirectory($tempDirPath);
});

it('can create exiftool command', function () {
    $commad = ExifReader::file('test.png')
        ->enableStructuredInformation()
        ->createExifToolCommand();

    expect($commad)->toBe([
        'file' => 'test.png',
        'options' => [
            '-struct',
        ],
    ]);
});

it('can extract xmp tags', function () {
    $commad = ExifReader::file('test.png')
        ->extractXMPInformation()
        ->createExifToolCommand();

    expect($commad)->toBe([
        'file' => 'test.png',
        'options' => [
            '-xmp:all'
        ],
    ]);

    $commad = ExifReader::file('test.png')
        ->extractXMPInformation('createDate')
        ->createExifToolCommand();

    expect($commad)->toBe([
        'file' => 'test.png',
        'options' => [
            '-xmp:createDate'
        ],
    ]);
});

it('can get image data hash', function () {
    $commad = ExifReader::file('test.png')
        ->extractHashOfTheImage()
        ->createExifToolCommand();

    expect($commad)->toBe([
        'file' => 'test.png',
        'options' => [
            '-ImageDataHash'
        ],
    ]);
});

it('can allow duplicate', function () {
    $commad = ExifReader::file('test.png')
        ->allowDuplicates()
        ->createExifToolCommand();

    expect($commad)->toBe([
        'file' => 'test.png',
        'options' => [
            '-a'
        ],
    ]);
});


it('can disable printo conversion', function () {
    $commad = ExifReader::file('test.png')
        ->disablePrintConversion()
        ->createExifToolCommand();

    expect($commad)->toBe([
        'file' => 'test.png',
        'options' => [
            '-n'
        ],
    ]);
});

it('can set csv output', function () {
    $commad = ExifReader::file('test.png')
        ->setCSV("test.csv")
        ->createExifToolCommand();

    expect($commad)->toBe([
        'file' => 'test.png',
        'options' => [
            '-csv>test.csv'
        ],
    ]);
});


it('can set json output', function () {
    $commad = ExifReader::file('test.png')
        ->setJSON("test.json")
        ->createExifToolCommand();

    expect($commad)->toBe([
        'file' => 'test.png',
        'options' => [
            '-json>test.json'
        ],
    ]);
});

it('can set recursively', function () {
    $commad = ExifReader::file('test.png')
        ->recursively()
        ->createExifToolCommand();

    expect($commad)->toBe([
        'file' => 'test.png',
        'options' => [
            '-r'
        ],
    ]);
});

it('can save metadata to CSV file', function () {
    $filePath = __DIR__ . '/testfile/BlueSquare.jpg';
    $targetPath = __DIR__ . '/temp/BlueSquare.csv';

    ExifReader::file($filePath)
        ->extractXMPInformation()
        ->saveCsv($targetPath);

    expect($targetPath)->toBeFile();

});

it('can save metadata to JSON file', function () {
    $filePath = __DIR__ . '/testfile/BlueSquare.jpg';
    $targetPath = __DIR__ . '/temp/BlueSquare.json';

    ExifReader::file($filePath)
        ->extractXMPInformation()
        ->saveJSON($targetPath);

    expect($targetPath)->toBeFile();

});

it('can scan dir recursively save json', function () {
    $dirPath = __DIR__ . '/testfile/testdir';

    ExifReader::folder($dirPath)
        ->setTargetBasePath(__DIR__ . '/temp')
        ->extractXMPInformation()
        ->saveJSON();

    expect(__DIR__ . '/temp/testdir.json')->toBeFile();
//        ->json()
//        ->toHaveCount(2);

})->only();


it('can scan dir recursively save csv', function () {
    $dirPath = __DIR__ . '/testfile/testdir';
    $targetPath = __DIR__ . '/temp/dir.csv';

    ExifReader::folder($dirPath)
        ->extractXMPInformation()
        ->saveCsv($targetPath);

    expect($targetPath)->toBeFile();

});