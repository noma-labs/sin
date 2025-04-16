<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Controllers\PersonaUscitaController;
use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;
use Carbon\Carbon;

it('exit a persona', function (): void {
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $act = app(EntrataMaggiorenneConFamigliaAction::class);
    $act->execute($persona, Carbon::now()->startOfDay(), GruppoFamiliare::all()->random());

    login();

    $data_uscita = Carbon::now()->startOfDay();

    $this->post(action([PersonaUscitaController::class, 'store'], ['idPersona' => $persona->id]),
        [
            'data_uscita' => $data_uscita->toDateString(),
        ])
        ->assertRedirect()
        ->assertRedirectContains(route('nomadelfia.person.show', ['id' => $persona->id]));

    $p = Persona::findOrFail($persona->id);
    expect($p->getDataUscitaNomadelfia())->toEqual($data_uscita)
        ->and($p->isPersonaInterna())->toBeFalse();
});
