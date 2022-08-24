<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon;

use Tests\MigrateFreshDB;
use Tests\CreatesApplication;


use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;;

class PersonaPosizioniTest extends TestCase
{
    use CreatesApplication, MigrateFreshDB;

    public function testAssignPosizione()
    {
        $data_entrata = Carbon::now()->toDatestring();
        $persona = Persona::factory()->maggiorenne()->maschio()->create();
        $gruppo = GruppoFamiliare::first();
        $persona->entrataMaggiorenneSingle($data_entrata, $gruppo->id);

        $data_inizio= Carbon::now()->addYears(5)->toDatestring();
        $data_fine= Carbon::now()->addYears(3)->toDatestring();
        $postulante = Posizione::perNome("postulante");

        $persona->assegnaPosizione($postulante, $data_inizio, $data_fine);

        $this->assertEquals($persona->posizioneAttuale()->id, $postulante->id);
        $this->assertEquals($persona->posizioniStorico()->first()->pivot->data_fine, $data_fine);
    }

    public function testModificaDataPosizione()
    {
        $data_entrata = Carbon::now()->toDatestring();
        $persona = Persona::factory()->maggiorenne()->maschio()->create();
        $gruppo = GruppoFamiliare::first();
        $persona->entrataMaggiorenneSingle($data_entrata, $gruppo->id);

        $data_inizio = Carbon::now()->addYears(5)->toDatestring();
        $data_fine = Carbon::now()->addYears(3)->toDatestring();
        $postulante = Posizione::perNome("postulante");

        $persona->assegnaPosizione($postulante, $data_inizio, $data_fine);
        $new_data_inizio = Carbon::now()->addYears(6)->toDatestring();

        $persona->modificaDataInizioPosizione($postulante->id, $data_inizio, $new_data_inizio);

        $this->assertEquals($persona->posizioneAttuale()->id, $postulante->id);
        $this->assertEquals($persona->posizioneAttuale()->pivot->data_inizio, $new_data_inizio);
        // $this->assertEquals($persona->posizioneAttuale()->pivot->data_fine, $data_fine);
    }
}
