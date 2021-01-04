<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Models\GruppoFamiliare;
use App\Nomadelfia\Models\Famiglia;

use Carbon;

class FamigliaTest extends TestCase
{
    /**
     * Test se l'uscita dal nucleo familiare di un figlio.
     *
     * */
    public function testUscitaFiglioDalNucleo()
    {
        $now = Carbon::now()->toDatestring();
        $gruppo = GruppoFamiliare::all()->random();

        $famiglia = factory(Famiglia::class)->create();

        $capoFam = factory(Persona::class)->states("maggiorenne", "maschio")->create();
        $moglie = factory(Persona::class)->states("maggiorenne", "femmina")->create();
        $fnato = factory(Persona::class)->states("minorenne", "femmina")->create();
        $faccolto = factory(Persona::class)->states("minorenne", "maschio")->create();

        $capoFam->entrataMaggiorenneSposato($now, $gruppo->id);
        $moglie->entrataMaggiorenneSposato($now, $gruppo->id);
        $famiglia->assegnaCapoFamiglia($capoFam, $now);
        $famiglia->assegnaMoglie($moglie, $now);

        $fnato->entrataNatoInNomadelfia($famiglia->id);
        $faccolto->entrataMinorenneAccolto(Carbon::now()->addYears(2)->toDatestring(), $famiglia->id);

        $this->assertEquals( 4, $famiglia->componentiAttuali()->get()->count());

        // toglie un figlio dal nucleo familiare
        $famiglia->uscitaDalNucleoFamiliare($fnato, Carbon::now()->addYears(4)->toDatestring(), "test remove from nucleo");
        
        $this->assertEquals(3, $famiglia->componentiAttuali()->get()->count());
    }
}
