<?php

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\Persona\Controllers\PersonaDecessoController;
use Carbon\Carbon;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;

it('stores a new decesso', function (): void {
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $act = app(EntrataMaggiorenneConFamigliaAction::class);
    $act->execute($persona, Carbon::now()->toDatestring(), GruppoFamiliare::all()->random());

    $data_decesso = Carbon::now()->toDateString();
    login();

    $this->post(action([PersonaDecessoController::class, 'store'], ['idPersona' => $persona->id]),
        [
            'data_decesso' => $data_decesso,
        ])
        ->assertRedirect()
        ->assertRedirectContains(route('nomadelfia.persone.dettaglio', ['idPersona' => $persona->id]));

    $p = Persona::findOrFail($persona->id);
    expect($p->data_decesso)->toBe($data_decesso)
        ->and($p->isPersonaInterna())->toBeFalse();
});
