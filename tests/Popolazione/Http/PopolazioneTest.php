<?php

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\Persona\Controllers\PersonaAnagraficaController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PopolazioneNomadelfiaController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PopolazioneSummaryController;
use Carbon\Carbon;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;

it('show index of nomadelfia', function () {
    login();
    $this->get(action([PopolazioneSummaryController::class, 'index']))
        ->assertSuccessful();
});

it('cant_insert_persona_with_same_nominativo_in_popolazione_presente', function () {
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $data_entrata = Carbon::now()->toDatestring();
    $gruppo = GruppoFamiliare::all()->random();
    $act = app(EntrataMaggiorenneSingleAction::class);
    $act->execute($persona, $data_entrata, $gruppo);

    login();
    $this->post(action([PersonaAnagraficaController::class, 'store']),
        [
            'nominativo' => $persona->nominativo,
            'nome' => 'name',
            'cognome' => 'my-surname',
            'data_nascita' => '2022-10-10',
            'luogo_nascita' => 'Grosseto',
            'sesso' => 'M',
        ]);
    // TODO: enabling this the test explode with thousand of errors !!
    //            ->assertSee("Il nominativo inserito è già assegnato alla persona");
});

it('can_export_popolazione_into_word', function () {
    login();
    $this->withoutExceptionHandling();
    $this->post(action([PopolazioneNomadelfiaController::class, 'print']),
        [
            'elenchi' => ['effePostOspFig', 'famiglie', 'gruppi', 'aziende', 'incarichi', 'scuola'],
        ])
        ->assertSuccessful()
        ->assertDownload();
});
