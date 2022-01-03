<?php

namespace Tests\Unit;

use App\Nomadelfia\Models\Azienda;
use App\Nomadelfia\Models\Incarico;
use App\Nomadelfia\Models\Persona;
use Tests\CreatesApplication;
use Tests\MigrateFreshDB;
use Tests\TestCase;
use Carbon;


class IncarichiTest extends TestCase
{
    use CreatesApplication, MigrateFreshDB;

    public function testIncarichi()
    {
        $persona = factory(Persona::class)->states("maggiorenne", "maschio")->create();
        $incarico1 = factory(Incarico::class)->create();
        $incarico = factory(Incarico::class)->create();

        $this->assertEquals(0, $incarico->lavoratori()->count());
        $this->assertEquals(0, $incarico->lavoratoriAttuali()->count());
        $this->assertEquals(0, $incarico->lavoratoriStorici()->count());
        $this->assertEquals(0, $persona->aziendeAttuali()->count());
        $this->assertEquals(0, $persona->aziendeStorico()->count());
        $this->assertEquals(0, $persona->incarichiAttuali()->count());
        $this->assertEquals(0, $persona->incarichiStorico()->count());


        $data_inizio = Carbon::now()->addYears(5);
        $persona->assegnaLavoratoreIncarico($incarico, $data_inizio);
        $this->assertEquals(1, $incarico->lavoratoriAttuali()->count());

        $this->assertEquals(1, $persona->incarichiAttuali()->count());
        $this->assertEquals(0, $persona->aziendeAttuali()->count());

//        $i = $persona->incarichiPossibili();
//        $this->assertEquals(1, count($i));
    }

    /** @test */
    public function when_incarico_isdeleted_all_lavoratori_are_deleted()
    {
//        $persona = factory(Persona::class)->states("maggiorenne", "maschio")->create();
//        $incarico = factory(Incarico::class)->create();
//
//        $data_inizio = Carbon::now()->addYears(5);
//        $persona->assegnaLavoratoreIncarico($incarico, $data_inizio);
//        $this->assertEquals(1, $incarico->lavoratoriAttuali()->count());

    }
}
