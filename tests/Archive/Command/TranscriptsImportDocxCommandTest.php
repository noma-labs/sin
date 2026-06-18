<?php

declare(strict_types=1);

use App\Console\Commands\TranscriptsImportDocxCommand;

// it('extracts the code from the heading first token', function (string $heading, string $expectedCode): void {
//     $command = new TranscriptsImportDocxCommand;

//     $extractCodeMethod = new ReflectionMethod(TranscriptsImportDocxCommand::class, 'extractCode');
//     $extractCodeMethod->setAccessible(true);

//     $code = $extractCodeMethod->invoke($command, $heading);

//     expect($code)->toBe($expectedCode);
// })->with([
//     ['491208', '491208'],
//     ['53010200 ANOTHER DOC', '53010200'],
//     ['5301010A MY DOC', '5301010A'],
//     [' 50120900 WITH SPACE', '50120900'],
//     ['   50120900   WITH MULTIPLE SPACES', '50120900'],
//     ["\t50120900 WITH TAB", '50120900'],
// ]);
