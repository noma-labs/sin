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

    }

    /** @test */
    public function it_get_the_most_busy_people(){
        $busyPeaple = factory(Persona::class)->states("maggiorenne", "maschio")->create();

        $num = 10;
        for ($i = 1; $i <= $num; $i++) {
            $incarico = factory(Incarico::class)->create();
            $busyPeaple->assegnaLavoratoreIncarico($incarico, Carbon::now());
        }

        $p = Incarico::getBusyPeople();
        $this->assertEquals($busyPeaple->id, $p[0]->id);
        $this->assertEquals(10, $p[0]->count);

    }

}
