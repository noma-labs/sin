<?php

namespace Tests\Unit;

use App\Nomadelfia\Models\Azienda;
use App\Nomadelfia\Models\Persona;
use Tests\CreatesApplication;
use Tests\MigrateFreshDB;
use Tests\TestCase;
use Carbon\Carbon;


class AziendaTest extends TestCase
{
    use CreatesApplication, MigrateFreshDB;

    public function testAzienda()
    {
        $azienda = Azienda::factory()->create();
        $persona = Persona::factory()->maggiorenne()->maschio()->create();

        $this->assertEquals(0, $azienda->lavoratori()->count());
        $this->assertEquals(0, $azienda->lavoratoriAttuali()->count());
        $this->assertEquals(0, $azienda->lavoratoriStorici()->count());
        $this->assertEquals(0, $persona->aziendeAttuali()->count());
        $this->assertEquals(0, $persona->aziendeStorico()->count());


        $data_inizio = Carbon::now()->addYears(5)->toDatestring();
        $persona->assegnaLavoratoreAzienda($azienda, $data_inizio);
        $this->assertEquals(1, $azienda->lavoratoriAttuali()->count());

        $resp = Persona::factory()->maggiorenne()->maschio()->create();
        $resp->assegnaResponsabileAzienda($azienda, $data_inizio);
        $this->assertEquals(2, $azienda->lavoratoriAttuali()->count());

        $data_uscita = Carbon::now()->addYears(5)->toDatestring();
        $persona->uscita($data_uscita);

        $this->assertEquals(1, $azienda->lavoratoriAttuali()->count());
        $this->assertEquals(1, $azienda->lavoratoriStorici()->count());

    }
}
