<?php

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\PopolazioneNomadelfia\Controllers\AggiornamentoAnagrafeController;
use Carbon\Carbon;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\LogEntrataPersonaAction;

it('show aggiornamento anagrafe index', function () {
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->minorenne()->femmina()->numeroElenco('AAA53')->luogoNascita('grosseto')->create();
    $famiglia = Famiglia::factory()->create();
    $gruppo = GruppoFamiliare::first();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
    $famiglia->assegnaCapoFamiglia($capoFam, $data_entrata);

    $action = app(LogEntrataPersonaAction::class);

    $action->execute(
        $persona,
        $data_entrata,
        $gruppo,
        $famiglia
    );
    login();
    $this->get(action([AggiornamentoAnagrafeController::class, 'index']))
        ->assertSuccessful()
        ->assertSee($persona->nominativo);
});
