<?php

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\Persona\Controllers\PersonaAnagraficaController;
use App\Nomadelfia\Persona\Controllers\PersonaDecessoController;
use App\Nomadelfia\Persona\Controllers\PersoneController;
use Carbon\Carbon;
use Domain\Nomadelfia\Persona\Models\Persona;


it('stores a new decesso', function () {
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $data_decesso = Carbon::now()->toDateString();
    login();

    $this->post(action([PersonaDecessoController::class, 'store'], ['idPersona' => $persona->id]),
        [
            'data_decesso' => $data_decesso
        ])
        ->assertRedirect()
        ->assertRedirectContains(route('nomadelfia.persone.dettaglio', ['idPersona' => $persona->id]));

    $p = Persona::findOrFail($persona->id);
    expect($p->data_decesso)->toBe($data_decesso)
        ->and($p->isPersonaInterna())->toBeFalse();
});
