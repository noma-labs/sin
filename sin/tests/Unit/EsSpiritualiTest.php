<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Nomadelfia\Models\EserciziSpirituali;
use App\Nomadelfia\Models\Persona;

use App\Nomadelfia\Exceptions\EsSpiritualeNotActive;

class EsSpiritualiTest extends TestCase
{
    public function testAggiungiPersona()
    {
        $esercizi = factory(EserciziSpirituali::class)->create();
        $persona = factory(Persona::class)->states("maggiorenne", "maschio")->create();
        $esercizi->aggiungiPersona($persona);
        $this->assertEquals($esercizi->persone->count(), 1);
        //$this->assertEquals($persona->eserciziSpiritualiAttuale()->count(), 1);
    }

    public function testEsSpiritualeNotActive()
    {
        $esercizi = factory(EserciziSpirituali::class)->state("disattivo")->create();
        $persona = factory(Persona::class)->states("maggiorenne", "maschio")->create();
        $this->expectException(EsSpiritualeNotActive::class);
        $esercizi->aggiungiPersona($persona);
    }
}
