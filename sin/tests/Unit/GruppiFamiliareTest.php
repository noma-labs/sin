<?php

namespace Tests\Unit;

use App\Nomadelfia\Exceptions\CouldNotAssignCapogruppo;
use App\Nomadelfia\Models\GruppoFamiliare;
use App\Nomadelfia\Models\Persona;
use Tests\TestCase;
use Carbon\Carbon;

class GruppiFamiliareTest extends TestCase
{
    public function testAssegnaCapoGruppo()
    {
        $gruppo = factory(GruppoFamiliare::class)->create();
        $data_entrata = Carbon::now();
        $persona = factory(Persona::class)->states("cinquantenne", "maschio")->create();
        $persona->entrataMaggiorenneSingle($data_entrata, $gruppo->id);
        $persona->assegnaPostulante($data_entrata);
        $persona->assegnaNomadelfoEffettivo($data_entrata);
        $gruppo->assegnaCapogruppo($persona, $data_entrata);
        $this->assertEquals($persona->id, $gruppo->capogruppoAttuale()->id);
    }

    public function testAssegnaCapogruppoErrorsWithPostulante()
    {
        $gruppo = factory(GruppoFamiliare::class)->create();
        $data_entrata = Carbon::now();
        $persona = factory(Persona::class)->states("cinquantenne", "maschio")->create();
        $persona->entrataMaggiorenneSingle($data_entrata, $gruppo->id);
        $persona->assegnaPostulante($data_entrata);
        $this->expectException(CouldNotAssignCapogruppo::class);
        $gruppo->assegnaCapogruppo($persona, $data_entrata);
        $this->assertEquals(null, $gruppo->capogruppoAttuale());
    }

    public function testAssegnaCapogruppoErrorsWithOspite()
    {
        $gruppo = factory(GruppoFamiliare::class)->create();
        $data_entrata = Carbon::now()->toDatestring();
        $persona = factory(Persona::class)->states("cinquantenne", "maschio")->create();
        $persona->entrataMaggiorenneSingle($data_entrata, $gruppo->id);
        $this->expectException(CouldNotAssignCapogruppo::class);
        $gruppo->assegnaCapogruppo($persona, $data_entrata);
        $this->assertEquals(null, $gruppo->capogruppoAttuale());
    }

    public function testAssegnaCapogruppoErrorsWithWomen()
    {
        $gruppo = factory(GruppoFamiliare::class)->create();
        $data_entrata = Carbon::now()->toDatestring();
        $persona = factory(Persona::class)->states("cinquantenne", "femmina")->create();
        $persona->entrataMaggiorenneSingle($data_entrata, $gruppo->id);
        $this->expectException(CouldNotAssignCapogruppo::class);
        $gruppo->assegnaCapogruppo($persona, $data_entrata);
        $this->assertEquals(null, $gruppo->capogruppoAttuale());
    }


    /** @test */
    public function uscita_persona_rimuove_dal_gruppo_familiare()
    {
        $gruppo = factory(GruppoFamiliare::class)->create();
        $now = Carbon::now();
        $data_entrata = $now->subYear(5)->toDatestring();
        $persona = factory(Persona::class)->states("maggiorenne", "maschio")->create();
        $persona->entrataMaggiorenneSingle($data_entrata, $gruppo->id);
        $data_uscita = $now;
        $this->assertEquals(1, $gruppo->personeAttuale->count());
        $persona->uscita($data_uscita);
        $this->assertEquals(0, $gruppo->personeAttuale()->count());

    }

}
