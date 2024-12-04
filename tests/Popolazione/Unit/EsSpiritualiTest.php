<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Nomadelfia\Exceptions\EsSpiritualeNotActive;
use Domain\Nomadelfia\EserciziSpirituali\Models\EserciziSpirituali;
use Domain\Nomadelfia\Persona\Models\Persona;

it('testAggiungiPersona', function (): void {
    $esercizi = EserciziSpirituali::factory()->create();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $esercizi->aggiungiPersona($persona);
    expect($esercizi->persone->count())->toBe(1);
    //expect($persona->eserciziSpiritualiAttuale()->count(), 1);
});

it('testEsSpiritualeNotActive', function (): void {
    $esercizi = EserciziSpirituali::factory()->disattivo()->create();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $esercizi->aggiungiPersona($persona);
})->throws(EsSpiritualeNotActive::class);

it('testAssegnaResponsabile', function (): void {
    $esercizi = EserciziSpirituali::factory()->create();
    $resp = Persona::factory()->maggiorenne()->maschio()->create();
    $esercizi->assegnaResponsabile($resp);
    expect($esercizi->responsabile->id)->toBe($resp->id);
});
