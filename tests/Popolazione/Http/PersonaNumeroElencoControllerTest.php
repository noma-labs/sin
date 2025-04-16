<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Nomadelfia\Persona\Controllers\FolderNumberController;
use App\Nomadelfia\Persona\Models\Persona;

it('show the form to assign a numero di elenco', function (): void {
    $persona = Persona::factory()->cognome('bob')->create();

    login();

    $this->get(action([FolderNumberController::class, 'create'],$persona->id))
        ->assertOk()
        ->assertSee('B1');

    $this->post(action([FolderNumberController::class, 'store'], $persona->id),
        [
            'numero_elenco' => 'B1',
        ])
        ->assertRedirectContains(route('nomadelfia.person.show', $persona->id));

    $persona = Persona::factory()->cognome('billy')->create();

    $this->get(action([FolderNumberController::class, 'create'],$persona->id))
        ->assertOk()
        ->assertSee('B2');

});
