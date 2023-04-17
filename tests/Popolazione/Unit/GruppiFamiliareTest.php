<?php

namespace Tests\Unit;

use App\Nomadelfia\Exceptions\CouldNotAssignCapogruppo;
use Carbon\Carbon;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\SaveEntrataInNomadelfiaAction;

it('testAssegnaCapoGruppo', function () {
    $gruppo = GruppoFamiliare::factory()->create();
    $data_entrata = Carbon::now();
    $persona = Persona::factory()->cinquantenne()->maschio()->create();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));

    $persona->assegnaPostulante($data_entrata);
    $persona->assegnaNomadelfoEffettivo($data_entrata);
    $gruppo->assegnaCapogruppo($persona, $data_entrata);
    expect($gruppo->capogruppoAttuale()->id)->toBe($persona->id);
});

it('testAssegnaCapogruppoErrorsWithPostulante', function () {
    $gruppo = GruppoFamiliare::factory()->create();
    $data_entrata = Carbon::now();
    $persona = Persona::factory()->cinquantenne()->maschio()->create();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));
    $persona->assegnaPostulante($data_entrata);
    $this->expectException(CouldNotAssignCapogruppo::class);
    $gruppo->assegnaCapogruppo($persona, $data_entrata);
    expect($gruppo->capogruppoAttuale())->toBeNull();
});

it('testAssegnaCapogruppoErrorsWithOspite', function () {
    $gruppo = GruppoFamiliare::factory()->create();
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->cinquantenne()->maschio()->create();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));
    $this->expectException(CouldNotAssignCapogruppo::class);
    $gruppo->assegnaCapogruppo($persona, $data_entrata);
    expect($gruppo->capogruppoAttuale())->toBeNull();
});

it('testAssegnaCapogruppoErrorsWithWomen', function () {
    $gruppo = GruppoFamiliare::factory()->create();
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->cinquantenne()->femmina()->create();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));
    $this->expectException(CouldNotAssignCapogruppo::class);
    $gruppo->assegnaCapogruppo($persona, $data_entrata);
    expect($gruppo->capogruppoAttuale())->toBeNull();
});
