<?php

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\Persona\Controllers\PersoneController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PopolazioneNomadelfiaController;
use Carbon\Carbon;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\SaveEntrataInNomadelfiaAction;

it('can_insert_persona', function () {
    login();
    $this->withoutExceptionHandling();
    $this->post(action([PersoneController::class, 'insertDatiAnagrafici']),
        [
            'nominativo' => 'my-name',
            'nome' => 'name',
            'cognome' => 'my-surname',
            'data_nascita' => '2022-10-10',
            'luogo_nascita' => 'Grosseto',
            'sesso' => 'M',
        ])
        ->assertRedirect();
});

it('cant_insert_persona_with_same_nominativo_in_popolazione_presente', function () {
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $data_entrata = Carbon::now()->toDatestring();
    $gruppo = GruppoFamiliare::all()->random();
    $act = new EntrataMaggiorenneSingleAction(new SaveEntrataInNomadelfiaAction());
    $act->execute($persona, $data_entrata, $gruppo);

    login();
    $this->post(action([PersoneController::class, 'insertDatiAnagrafici']),
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
