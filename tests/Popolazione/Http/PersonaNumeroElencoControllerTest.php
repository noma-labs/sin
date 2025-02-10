<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\Persona\Controllers\PersonaNumeroElencoController;
use Domain\Nomadelfia\Persona\Models\Persona;

it('show the form to assign a numero di elenco', function (): void {
    $persona = Persona::factory()->cognome('bob')->create();

    login();

    $this->get(action([PersonaNumeroElencoController::class, 'edit'], ['idPersona' => $persona->id]))
        ->assertOk()
        ->assertSee('B1');

    $this->put(action([PersonaNumeroElencoController::class, 'update'], ['idPersona' => $persona->id]),
        [
            'numero_elenco' => 'B1',
        ])
        ->assertRedirectContains(route('nomadelfia.persone.dettaglio', ['idPersona' => $persona->id]));

    $persona = Persona::factory()->cognome('billy')->create();

    $this->get(action([PersonaNumeroElencoController::class, 'edit'], ['idPersona' => $persona->id]))
        ->assertOk()
        ->assertSee('B2');

});
