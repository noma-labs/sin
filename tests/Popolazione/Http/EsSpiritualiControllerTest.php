<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\EserciziSpirituali\Controllers\EsSpiritualiController;
use App\Nomadelfia\EserciziSpirituali\Models\EserciziSpirituali;

it('adds and removes a person from the esercizi spirituali', function (): void {
    $esercizi = EserciziSpirituali::factory()->create();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    login();

    $this->post(action([EsSpiritualiController::class, 'assegnaPersona'], $esercizi->id), ['persona_id' => $persona->id])
        ->assertRedirect()
        ->assertSessionHas('success');
    expect($esercizi->persone()->count())->toBe(1);

    $this->delete(action([EsSpiritualiController::class, 'eliminaPersona'], [$esercizi->id, $persona->id]))
        ->assertRedirect()
        ->assertSessionHas('success');
    expect($esercizi->persone()->count())->toBe(0);
});
