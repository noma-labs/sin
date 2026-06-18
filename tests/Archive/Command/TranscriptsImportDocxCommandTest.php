<?php

declare(strict_types=1);

use App\Console\Commands\TranscriptsImportDocxCommand;

it('extracts the code from the heading first token', function (string $heading, string $expectedCode): void {
    $command = new TranscriptsImportDocxCommand;

    $extractCodeMethod = new ReflectionMethod(TranscriptsImportDocxCommand::class, 'extractCode');
    $extractCodeMethod->setAccessible(true);

    $code = $extractCodeMethod->invoke($command, $heading);

    expect($code)->toBe($expectedCode);
})->with([
    ['5301010A IL FIGLIOL PRODIGO', '5301010A'],
    ['53010200 LA FAMIGLIA CHE VIVE NELLA FELICITA\'', '53010200'],
    ['491208 INVITO', '491208'],
    ['4912090A LA RICERCA DELLA FELICITA\'', '4912090A'],
    [' 50120900 CON SPAZIO', '50120900'],
    ['   50120900   CON MULTIPLI  SPAZI', '50120900'],
    ["\t50120900 CON TAB", '50120900'],
])->only();
