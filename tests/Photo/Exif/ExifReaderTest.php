<?php

use Domain\Photo\Exif\ExifReader;
use Domain\Photo\Models\ExifData;

beforeEach(function () {
    $tempDirPath = __DIR__.'/temp';
    $this->emptyTempDirectory($tempDirPath);
});

it('can create exiftool command', function () {
    $command = ExifReader::file('test.png')
        ->enableStructuredInformation()
        ->createExifToolCommand();

    expect($command)->toBe([
        'file' => 'test.png',
        'options' => [
            '-struct',
        ],
    ]);
});

it('can create command with xmp options', function () {
    $command = ExifReader::file('test.png')
        ->extractXMPInformation()
        ->createExifToolCommand();

    expect($command)->toBe([
        'file' => 'test.png',
        'options' => [
            '-xmp:all',
        ],
    ]);

    $command = ExifReader::file('test.png')
        ->extractXMPInformation('createDate')
        ->createExifToolCommand();

    expect($command)->toBe([
        'file' => 'test.png',
        'options' => [
            '-xmp:createDate',
        ],
    ]);
});

it('can create command with imageDataHash', function () {
    $commad = ExifReader::file('test.png')
        ->extractHashOfTheImage()
        ->createExifToolCommand();

    expect($commad)->toBe([
        'file' => 'test.png',
        'options' => [
            '-ImageDataHash',
        ],
    ]);
});

it('can create command wit allow duplicate', function () {
    $commad = ExifReader::file('test.png')
        ->allowDuplicates()
        ->createExifToolCommand();

    expect($commad)->toBe([
        'file' => 'test.png',
        'options' => [
            '-a',
        ],
    ]);
});

it('can create command with no Print conversion', function () {
    $commad = ExifReader::file('test.png')
        ->disablePrintConversion()
        ->createExifToolCommand();

    expect($commad)->toBe([
        'file' => 'test.png',
        'options' => [
            '-n',
        ],
    ]);
});

it('can can create command with csv format', function () {
    $commad = ExifReader::file('test.png')
        ->exportToCSV('test.csv')
        ->createExifToolCommand();

    expect($commad)->toBe([
        'file' => 'test.png',
        'options' => [
            '-csv>test.csv',
        ],
    ]);
});

it('can create command with json output', function () {
    $commad = ExifReader::file('test.png')
        ->exportToJSON('test.json')
        ->createExifToolCommand();

    expect($commad)->toBe([
        'file' => 'test.png',
        'options' => [
            '-json>test.json',
        ],
    ]);
});

it('can create command with recursive', function () {
    $commad = ExifReader::file('test.png')
        ->recursively()
        ->createExifToolCommand();

    expect($commad)->toBe([
        'file' => 'test.png',
        'options' => [
            '-r',
        ],
    ]);
});

it('can save exif data into CSV file', function () {
    $filePath = __DIR__.'/testfile/BlueSquare.jpg';
    $targetPath = __DIR__.'/temp/BlueSquare.csv';

    ExifReader::file($filePath)
        ->extractXMPInformation()
        ->saveCsv($targetPath);

    expect($targetPath)->toBeFile();

});

it('can save exif data into JSON file', function () {
    $filePath = __DIR__.'/testfile/BlueSquare.jpg';
    $targetPath = __DIR__.'/temp/BlueSquare.json';

    ExifReader::file($filePath)
        ->extractXMPInformation()
        ->saveJSON($targetPath);

    expect($targetPath)->toBeFile();
});

it('can scan a directory and save exif data into json', function () {
    $dirPath = __DIR__.'/testfile/testdir';

    ExifReader::folder($dirPath)
        ->setTargetBasePath(__DIR__.'/temp')
        ->extractXMPInformation()
        ->saveJSON();

    expect(__DIR__.'/temp/testdir.json')->toBeFile();
    //        ->json()
    //        ->toHaveCount(2);

});

it('can scan dir recursively save csv', function () {
    $dirPath = __DIR__.'/testfile/testdir';
    $targetPath = __DIR__.'/temp/dir.csv';

    ExifReader::folder($dirPath)
        ->extractXMPInformation()
        ->saveCsv($targetPath);

    expect($targetPath)->toBeFile();

});

it('can save to php', function () {
    $filePath = __DIR__.'/testfile/BlueSquare.jpg';

    $a = ExifReader::file($filePath)
        ->extractXMPInformation()
        ->savePhpArray();

    expect(count($a))->toBe(1);
    $info = $a[0];
    expect($info['Format'])->toBe('image/jpeg');

});

it('can build exifData from exif', function () {
    $filePath = __DIR__.'/testfile/BlueSquare.jpg';

    $info = ExifReader::file($filePath)
        ->extractXMPInformation()
        ->extractHashOfTheImage()
        ->extractFileGroup()
        ->disablePrintConversion()
        ->savePhpArray();

    $data = ExifData::fromArray($info[0]);
    expect($data->sourceFile)->toContain('BlueSquare.jpg');
    expect($data->fileSize)->toBe(24205);

});