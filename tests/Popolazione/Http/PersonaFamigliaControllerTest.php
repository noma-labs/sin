<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Anagrafe\Famiglia\Controllers\PersonaFamigliaController;
use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Persona\Models\Persona;

it('can render the famiglia index page of a person', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $this->get(action([PersonaFamigliaController::class, 'index'], $persona->id))
        ->assertSuccessful();
});

it('adds a person to a family as figlio', function (): void {
    login();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $famiglia = Famiglia::factory()->create();

    $this->post(action([PersonaFamigliaController::class, 'store'], $persona->id), [
        'family_id' => $famiglia->id,
        'type' => 'FIGLIO ACCOLTO',
    ])->assertRedirectToRoute('nomadelfia.person.show', $persona->id);

    expect($persona->famigliaAttuale()->id)->toBe($famiglia->id)
        ->and($persona->isFiglioAccolto())->toBeTrue();
});

it('refuses to add a person to a family as capofamiglia or moglie', function (string $posizione): void {
    login();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia = Famiglia::factory()->create();

    $this->post(action([PersonaFamigliaController::class, 'store'], $persona->id), [
        'family_id' => $famiglia->id,
        'type' => $posizione,
    ])->assertSessionHasErrors(['type' => 'La posizione deve essere figlio nato o figlio accolto. Capofamiglia e moglie si assegnano con un nuovo matrimonio.']);

    expect($persona->famigliaAttuale())->toBeNull();
})->with(['CAPO FAMIGLIA', 'MOGLIE']);
