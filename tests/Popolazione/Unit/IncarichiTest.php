<?php

namespace Tests\Unit;

use Carbon;
use Domain\Nomadelfia\Incarico\Models\Incarico;
use Domain\Nomadelfia\Persona\Models\Persona;

it('testIncarich', function () {
    $persona = Persona::factory()->cinquantenne()->maschio()->create();
    $incarico = Incarico::factory()->create();

    expect($incarico->lavoratori()->count())->toBe(0)
        ->and($incarico->lavoratoriAttuali()->count())->toBe(0)
        ->and($incarico->lavoratoriStorici()->count())->toBe(0)
        ->and($persona->aziendeAttuali()->count())->toBe(0)
        ->and($persona->aziendeStorico()->count())->toBe(0)
        ->and($persona->incarichiAttuali()->count())->toBe(0)
        ->and($persona->incarichiStorico()->count())->toBe(0);

    $data_inizio = Carbon::now()->addYears(5);
    $persona->assegnaLavoratoreIncarico($incarico, $data_inizio);
    expect($incarico->lavoratoriAttuali()->count())->toBe(1)
        ->and($persona->incarichiAttuali()->count())->toBe(1)
        ->and($persona->aziendeAttuali()->count())->toBe(0);

});

it('it_get_the_most_busy_people', function () {
    $busyPeaple = Persona::factory()->cinquantenne()->maschio()->create();

    $num = 10;
    for ($i = 1; $i <= $num; $i++) {
        $incarico = Incarico::factory()->create();
        $busyPeaple->assegnaLavoratoreIncarico($incarico, Carbon::now());
    }

    $p = Incarico::getBusyPeople();
    expect($busyPeaple->id)->toBe($p[0]->id)
        ->and($p[0]->count)->toBe(10);

});
