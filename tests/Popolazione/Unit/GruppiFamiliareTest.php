<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Nomadelfia\Exceptions\CouldNotAssignCapogruppo;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use Carbon\Carbon;

it('assign capogruppo', function (): void {
    $gruppo = GruppoFamiliare::factory()->create();
    $data_entrata = Carbon::now();
    $persona = Persona::factory()->cinquantenne()->maschio()->create();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));

    $persona->assegnaPosizione(Posizione::perNome('postulante'), $data_entrata);
    $persona->assegnaPosizione(Posizione::perNome('effettivo'), $data_entrata);
    $gruppo->assegnaCapogruppo($persona, $data_entrata);
    expect($gruppo->capogruppoAttuale()->id)->toBe($persona->id);
});

it('could not assign capogruppo if person is postulante', function (): void {
    $gruppo = GruppoFamiliare::factory()->create();
    $data_entrata = Carbon::now();
    $persona = Persona::factory()->cinquantenne()->maschio()->create();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));
    $persona->assegnaPosizione(Posizione::perNome('postulante'), $data_entrata);
    $this->expectException(CouldNotAssignCapogruppo::class);
    $gruppo->assegnaCapogruppo($persona, $data_entrata);
    expect($gruppo->capogruppoAttuale())->toBeNull();
});

it('testAssegnaCapogruppoErrorsWithOspite', function (): void {
    $gruppo = GruppoFamiliare::factory()->create();
    $data_entrata = Carbon::now()->startOfDay();
    $persona = Persona::factory()->cinquantenne()->maschio()->create();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));
    $this->expectException(CouldNotAssignCapogruppo::class);
    $gruppo->assegnaCapogruppo($persona, $data_entrata);
    expect($gruppo->capogruppoAttuale())->toBeNull();
});

it('testAssegnaCapogruppoErrorsWithWomen', function (): void {
    $gruppo = GruppoFamiliare::factory()->create();
    $data_entrata = Carbon::now()->startOfDay();
    $persona = Persona::factory()->cinquantenne()->femmina()->create();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));
    $this->expectException(CouldNotAssignCapogruppo::class);
    $gruppo->assegnaCapogruppo($persona, $data_entrata);
    expect($gruppo->capogruppoAttuale())->toBeNull();
});
