<?php

namespace Tests\Unit;

use App\Nomadelfia\Models\Azienda;
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
        $incarico1 = factory(Azienda::class)->states("incarico")->create();
        $incarico = factory(Azienda::class)->states("incarico")->create();

        $this->assertTrue($incarico->isIncarico());

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
}