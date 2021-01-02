<?php

namespace Tests\Unit;

use Tests\TestCase;
use Carbon;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Models\Famiglia;
use App\Nomadelfia\Models\GruppoFamiliare;
use App\Nomadelfia\Models\Posizione;
use App\Nomadelfia\Models\Stato;

class PersonaTest extends TestCase
{

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testEntrataMaggiorenneSingle()
    {
        $data_entrata = "2020-12-31";
        $persona = factory(Persona::class)->states("maggiorenne")->create();
        $gruppo = GruppoFamiliare::first();
        $ospite = Posizione::perNome("ospite");
        $celibe = Stato::perNome("celibe");

        $persona->entrataMaggiorenneSingle($data_entrata, $gruppo->id);

        $this->assertTrue($persona->isPersonaInterna());
        $this->assertEquals($persona->getDataEntrataNomadelfia(), $data_entrata);
        $this->assertEquals($persona->posizioneAttuale()->id, $ospite->id);
        $this->assertEquals($persona->posizioneAttuale()->pivot->data_inizio, $data_entrata);
        $this->assertEquals($persona->statoAttuale()->id, $celibe->id);
        $this->assertEquals($persona->statoAttuale()->pivot->data_inizio, $persona->data_nascita);
        $this->assertEquals($persona->gruppofamiliareAttuale()->id, $gruppo->id);
        $this->assertEquals($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo, $data_entrata);

        $this->assertNotNull($persona->famigliaAttuale());
        // check that the date creation of the family is when the person is 18 years old.
        $this->assertEquals($persona->famigliaAttuale()->data_creazione, Carbon::parse($persona->data_nascita)->addYears(18)->toDatestring());
    }
}
