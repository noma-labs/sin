<?php

namespace Tests\Unit;

use App\Nomadelfia\Models\GruppoFamiliare;
use App\Nomadelfia\Models\Persona;
use Tests\TestCase;
use Carbon\Carbon;

class GruppiFamiliareTest extends TestCase
{
    public function testCapoGruppoPossibili()
    {
        $gruppo = factory(GruppoFamiliare::class)->create();

        $data_entrata = Carbon::now()->toDatestring();
        $persona = factory(Persona::class)->states("cinquantenne", "maschio")->create();
        $persona->entrataMaggiorenneSingle($data_entrata, $gruppo->id);
        $gruppo->assegnaCapogruppo($persona, $data_entrata);

        $this->assertEquals(1, $gruppo->capogruppoAttuale()->count());


//
//        $data_entrata = Carbon::now()->toDatestring();
//        $persona = factory(Persona::class)->states("cinquantenne", "maschio")->create();
//        $persona->entrataMaggiorenneSingle($data_entrata, $gruppo->id);
//
//        $data_entrata = Carbon::now()->toDatestring();
//        $persona = factory(Persona::class)->states("cinquantenne", "femmina")->create();
//        $persona->entrataMaggiorenneSingle($data_entrata, $gruppo->id);
//
//        $this->assertEquals(1, $gruppo->capogruppiPossibili()->count());

    }


}
