<?php

namespace Tests\Unit;

use App\Nomadelfia\Models\Cariche;
use App\Nomadelfia\Models\Persona;
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
}
