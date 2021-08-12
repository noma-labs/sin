<?php

namespace Tests\Unit;

use App\Nomadelfia\Models\Cariche;
use App\Nomadelfia\Models\GruppoFamiliare;
use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Models\Posizione;
use App\Nomadelfia\Models\Stato;
use Tests\TestCase;
use Carbon;

class CaricheTest extends TestCase
{
    public function testCariche()
    {
        $this->assertEquals(12, count(Cariche::AssociazioneCariche()));
        $this->assertEquals(4, count(Cariche::SolidarietaCariche()));
    }

    public function testEliggibiliConsiglioAnziani()
    {
        // entrata maggiorenne maschio
        $data_entrata = Carbon::now()->toDatestring();
        $persona = factory(Persona::class)->states("cinquantenne", "maschio")->create();
        $gruppo = GruppoFamiliare::first();
        $persona->entrataMaggiorenneSingle($data_entrata, $gruppo->id);

        // Sacerdote: non deve essere contato negli eleggibili
        $data_entrata = Carbon::now();
        $persona = factory(Persona::class)->states("cinquantenne", "maschio")->create();
        $persona->assegnaSacerdote($data_entrata);
        $gruppo = GruppoFamiliare::first();
        $persona->entrataMaggiorenneSingle($data_entrata->toDatestring(), $gruppo->id);

        $ele = Cariche::EleggibiliConsiglioAnziani();
        $this->assertEquals(0, $ele->total);

        $persona->assegnaPostulante(Carbon::now()->SubYears(20));
        $persona->assegnaNomadelfoEffettivo(Carbon::now()->SubYears(12));


        $ele = Cariche::EleggibiliConsiglioAnziani();
        $this->assertEquals(1, $ele->total);

    }

}
