<?php

declare(strict_types=1);

use Domain\Photo\Exif\ExifReader;

beforeEach(function (): void {
    $tempDirPath = __DIR__.'/temp';
    $this->emptyTempDirectory($tempDirPath);
});

it('can create exiftool command', function (): void {
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

it('can create command with xmp options', function (): void {
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

it('can create command with imageDataHash', function (): void {
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

it('can create command wit allow duplicate', function (): void {
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

it('can create command with file order', function (): void {
    expect(ExifReader::file('test.png')->fileOrder('myTag')->createExifToolCommand())
        ->toBe([
            'file' => 'test.png',
            'options' => [
                '-fileOrder4 myTag',
            ],
        ])
        ->and(ExifReader::file('test.png')->fileOrder('myTag', '5', 'DESC')->createExifToolCommand())
        ->toBe([
            'file' => 'test.png',
            'options' => [
                '-fileOrder5 -myTag',
            ],
        ]);

});

it('can create command with verbose', function (): void {
    expect(ExifReader::file('test.png')->verbose(0)->createExifToolCommand())
        ->toBe([
            'file' => 'test.png',
            'options' => [
                '-v0',
            ],
        ])
        ->and(ExifReader::file('test.png')->verbose()->createExifToolCommand())
        ->toBe([
            'file' => 'test.png',
            'options' => [
                '-v5',
            ],
        ]);

});

it('can create command with no Print conversion', function (): void {
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

it('can can create command with csv format', function (): void {
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

it('can create command with json output', function (): void {
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

it('can create command with recursive', function (): void {
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

// it('can save exif data into CSV file', function () {
//    $filePath = __DIR__.'/testfile/BlueSquare.jpg';
//    $targetPath = __DIR__.'/temp/BlueSquare.csv';
//
//    ExifReader::file($filePath)
//        ->extractXMPInformation()
//        ->saveCsv($targetPath);
//
//    expect($targetPath)->toBeFile();
//
// });
//
// it('can save exif data into JSON file', function () {
//    $filePath = __DIR__.'/testfile/BlueSquare.jpg';
//    $targetFile = '/temp/BlueSquare.json';
//
//    $fullName = ExifReader::file($filePath)
//        ->setSourcePath(__DIR__)
//        ->extractXMPInformation()
//        ->saveJSON($targetFile);
//
//    expect($fullName)->toBeFile();
// });
//
// it('can scan a directory and save exif data into json', function () {
//    $dirPath = __DIR__.'/testfile/testdir';
//
//   $filePath= ExifReader::folder($dirPath)
//        ->extractXMPInformation()
//        ->saveJSON();
//
//    expect($filePath)->toBeFile();
// });

// it('can scan dir recursively save csv', function () {
//    $dirPath = __DIR__ . '/testfile/testdir';
//    $targetPath = __DIR__ . '/temp/dir.csv';
//
//    ExifReader::folder($dirPath)
//        ->extractXMPInformation()
//        ->saveCsv($targetPath);
//
//    expect($targetPath)->toBeFile();
//
// });

it('can save to php', function (): void {
    $filePath = __DIR__.'/testfile/BlueSquare.jpg';

    $a = ExifReader::file($filePath)
        ->extractXMPInformation()
        ->savePhpArray();

    expect(count($a))->toBe(1);
    $data = $a[0];
    expect($data->sourceFile)->toContain('BlueSquare.jpg');
});

it('can build exifData from exif', function (): void {
    $filePath = __DIR__.'/testfile/BlueSquare.jpg';

    $info = ExifReader::file($filePath)
        ->extractXMPInformation()
        ->extractHashOfTheImage()
        ->extractFileInformation()
        ->disablePrintConversion()
        ->savePhpArray();

    $data = $info[0];
    expect($data->sourceFile)->toContain('BlueSquare.jpg');
    expect($data->fileSize)->toBe(24205);

});
