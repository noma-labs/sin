<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Domain\Nomadelfia\EserciziSpirituali\Models\EserciziSpirituali;
use Domain\Nomadelfia\Persona\Models\Persona;

use App\Nomadelfia\Exceptions\EsSpiritualeNotActive;

class EsSpiritualiTest extends TestCase
{
    public function testAggiungiPersona()
    {
        $esercizi = EserciziSpirituali::factory()->create();
        $persona = Persona::factory()->maggiorenne()->maschio()->create();
        $esercizi->aggiungiPersona($persona);
        $this->assertEquals($esercizi->persone->count(), 1);
        //$this->assertEquals($persona->eserciziSpiritualiAttuale()->count(), 1);
    }

    public function testEsSpiritualeNotActive()
    {
        $esercizi = EserciziSpirituali::factory()->disattivo()->create();
        $persona = Persona::factory()->maggiorenne()->maschio()->create();
        $this->expectException(EsSpiritualeNotActive::class);
        $esercizi->aggiungiPersona($persona);
    }

    public function testAssegnaResponsabile()
    {
        $esercizi = EserciziSpirituali::factory()->create();
        $resp = Persona::factory()->maggiorenne()->maschio()->create();
        $esercizi->assegnaResponsabile($resp);
        $this->assertEquals($esercizi->responsabile->id, $resp->id);
    }
}
