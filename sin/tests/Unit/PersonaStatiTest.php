<?php

namespace Tests\Unit;
use Carbon;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\MigrateFreshDB;
use Tests\CreatesApplication;


use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Models\Famiglia;
use App\Nomadelfia\Models\GruppoFamiliare;
use App\Nomadelfia\Models\Posizione;
use App\Nomadelfia\Models\Stato;


class PersonaStatiTest extends TestCase
{
    use CreatesApplication, MigrateFreshDB;

    public function testAssignStatoSacerdote()
    {
        $data_entrata = Carbon::now()->toDatestring();
        $persona = factory(Persona::class)->states("maggiorenne", "maschio")->create();
        $gruppo = GruppoFamiliare::first();
        $persona->entrataMaggiorenneSingle($data_entrata, $gruppo->id);

        $data_inizio= Carbon::now()->addYears(5)->toDatestring();
        $data_fine= Carbon::now()->addYears(3)->toDatestring();
        $sac = Stato::perNome("sacerdote");

        $persona->assegnaStato($sac, $data_inizio, $data_fine);

        $this->assertEquals($persona->statoAttuale()->id, $sac->id);
        $this->assertEquals($persona->statiStorico()->first()->pivot->data_fine, $data_fine);
    }
}