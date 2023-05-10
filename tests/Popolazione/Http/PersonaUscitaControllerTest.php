<?php

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\Persona\Controllers\PersonaAnagraficaController;
use App\Nomadelfia\Persona\Controllers\PersonaDecessoController;
use App\Nomadelfia\Persona\Controllers\PersonaUscitaController;
use App\Nomadelfia\Persona\Controllers\PersoneController;
use Carbon\Carbon;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;


it('exit a persona', function () {
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $act = app(EntrataMaggiorenneConFamigliaAction::class);
    $act->execute($persona, Carbon::now()->toDatestring(), GruppoFamiliare::all()->random());
    login();

    $data_uscita = Carbon::now()->toDateString();

    $this->post(action([PersonaUscitaController::class, 'store'], ['idPersona' => $persona->id]),
        [
            'data_uscita' => $data_uscita
        ])
        ->assertRedirect()
        ->assertRedirectContains(route('nomadelfia.persone.dettaglio', ['idPersona' => $persona->id]));

    $p = Persona::findOrFail($persona->id);
    expect($p->getDataUscitaNomadelfia())->toBe($data_uscita)
        ->and($p->isPersonaInterna())->toBeFalse();
});
