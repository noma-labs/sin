<?php

namespace Tests\Unit;

use Tests\MigrateFreshDB;
use Tests\TestCase;
use Carbon;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesApplication;

use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Models\Azienda;
use App\Nomadelfia\Models\Famiglia;
use App\Nomadelfia\Models\GruppoFamiliare;
use App\Nomadelfia\Models\Posizione;
use App\Nomadelfia\Models\Stato;

class AziendaTest extends TestCase
{
    use CreatesApplication, MigrateFreshDB;

    public function testAzienda()
    {  
        $persona = factory(Persona::class)->states("maggiorenne", "maschio")->create();
        $azienda = factory(Azienda::class)->create();

        $this->assertEquals(0, $azienda->lavoratori()->count());
        $this->assertEquals(0, $azienda->lavoratoriAttuali()->count());
        $this->assertEquals(0, $azienda->lavoratoriStorici()->count());
        $this->assertEquals(0, $persona->aziendeAttuali()->count());
        $this->assertEquals(0, $persona->aziendeStorico()->count());




        
    }
}
