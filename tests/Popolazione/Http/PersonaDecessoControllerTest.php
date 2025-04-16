<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Controllers\PersonaDecessoController;
use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;
use Carbon\Carbon;

it('stores a new decesso', function (): void {
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $act = app(EntrataMaggiorenneConFamigliaAction::class);
    $act->execute($persona, Carbon::now(), GruppoFamiliare::all()->random());

    $data_decesso = Carbon::now()->toDateString();
    login();

    $this->post(action([PersonaDecessoController::class, 'store'], ['idPersona' => $persona->id]),
        [
            'data_decesso' => $data_decesso,
        ])
        ->assertRedirect()
        ->assertRedirectContains(route('nomadelfia.person.show', $persona->id));

    $p = Persona::findOrFail($persona->id);
    expect($p->data_decesso)->toEqual($data_decesso)
        ->and($p->isPersonaInterna())->toBeFalse();
});
