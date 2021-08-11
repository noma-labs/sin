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
        $persona = factory(Persona::class)->states("maggiorenne", "maschio")->create();
        $ass = Cariche::AssociazioneCariche();
        $this->assertEquals(12, count($ass));
        $this->assertEquals(4, count(Cariche::SolidarietaCariche()));
//        $c = $ass->random();
//        $c->assegnaMembro($persona,Carbon::now()->addYears(5)->toDatestring() );
//        $this->assertEquals(1, $c->membri()->count());

    }
    public function testEliggibiliConsiglioAnziani()
    {
        // entrata maggiorenne maschio
        $data_entrata = Carbon::now()->toDatestring();
        $persona = factory(Persona::class)->states("cinquantenne", "maschio")->create();
        $gruppo = GruppoFamiliare::first();
        $persona->entrataMaggiorenneSingle($data_entrata, $gruppo->id);

        $ele = Cariche::EleggibiliConsiglioAnziani();
        $this->assertEquals(0, $ele->total);

        $persona->assegnaPostulante(Carbon::now()->SubYears(20)->toDatestring());
        $persona->assegnaNomadelfoEffettivo(Carbon::now()->SubYears(12)->toDatestring());


        $ele = Cariche::EleggibiliConsiglioAnziani();
        $this->assertEquals(1, $ele->total);

    }

}
