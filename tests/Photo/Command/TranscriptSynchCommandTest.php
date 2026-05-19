<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;

it('syncs recordings code from data and ore', function (): void {
    DB::connection('archivio_nomadelfia')->table('recordings')->insert([
        [
            'DATA' => '1950-06-02',
            'ORE' => '11',
            'code' => null,
        ],
        [
            'DATA' => '1950-06-03',
            'ORE' => null,
            'code' => null,
        ],
    ]);

    $this->artisan('transcripts:sync')->assertSuccessful();

    $firstCode = DB::connection('archivio_nomadelfia')
        ->table('recordings')
        ->where('DATA', '1950-06-02')
        ->value('code');

    $secondCode = DB::connection('archivio_nomadelfia')
        ->table('recordings')
        ->where('DATA', '1950-06-03')
        ->value('code');

    expect($firstCode)->toBe('1950060211')
        ->and($secondCode)->toBe('1950060300');
});
