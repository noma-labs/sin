<?php

namespace Tests\Unit;

use App\Nomadelfia\Exceptions\EsSpiritualeNotActive;
use Domain\Nomadelfia\EserciziSpirituali\Models\EserciziSpirituali;
use Domain\Nomadelfia\Persona\Models\Persona;

it('testAggiungiPersona', function () {
    $esercizi = EserciziSpirituali::factory()->create();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $esercizi->aggiungiPersona($persona);
    $this->assertEquals($esercizi->persone->count(), 1);
    //$this->assertEquals($persona->eserciziSpiritualiAttuale()->count(), 1);
});

it('testEsSpiritualeNotActive', function () {
    $esercizi = EserciziSpirituali::factory()->disattivo()->create();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $this->expectException(EsSpiritualeNotActive::class);
    $esercizi->aggiungiPersona($persona);
});

it('testAssegnaResponsabile', function () {
    $esercizi = EserciziSpirituali::factory()->create();
    $resp = Persona::factory()->maggiorenne()->maschio()->create();
    $esercizi->assegnaResponsabile($resp);
    $this->assertEquals($esercizi->responsabile->id, $resp->id);
});
