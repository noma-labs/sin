<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Anagrafe\Famiglia\DataTransferObjects\AlberoFamiliare;
use App\Anagrafe\Persona\Models\Persona;
use Carbon\Carbon;

it('tells how a persona entered a famiglia as figlio', function (string $sesso, string $posizione, ?string $dataEntrata, string $etichetta): void {
    $persona = Persona::factory()->nato(Carbon::parse('1980-04-10'))->make(['sesso' => $sesso]);

    expect(AlberoFamiliare::etichettaTappa($persona, $posizione, $dataEntrata))->toBe($etichetta);
})->with([
    'figlio nato, from the birth' => ['M', 'FIGLIO NATO', null, 'nato 1980'],
    'figlia accolta with the data di entrata' => ['F', 'FIGLIO ACCOLTO', '1992-03-01', 'accolta 1992'],
    'aggregata without the data di entrata' => ['F', 'AGGREGATO', null, 'aggregata'],
]);
