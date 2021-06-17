<?php

namespace Tests\Unit;

use App\Nomadelfia\Exceptions\CouldNotAssignCapoFamiglia;
use App\Nomadelfia\Exceptions\CouldNotAssignMoglie;
use InvalidArgumentException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\MigrateFreshDB;
use Tests\CreatesApplication;

use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Models\GruppoFamiliare;
use App\Nomadelfia\Models\Famiglia;

use Carbon;

class FamigliaTest extends TestCase
{
    use CreatesApplication, MigrateFreshDB;

    public function testAssegnaComponenteThrowException()
    {
        $famiglia = factory(Famiglia::class)->create();
        $persona = factory(Persona::class)->states("maggiorenne", "maschio")->create();

        $this->expectException(InvalidArgumentException::class);
        $famiglia->assegnaComponente($persona, "NOT EXISTING", Carbon::now()->toDatestring());
        $this->assertEquals(3,3);

    }

    public function testAssegnaComponente()
    {
        // assegna capo famiglia
        $famiglia = factory(Famiglia::class)->create();
        $persona = factory(Persona::class)->states("maggiorenne", "maschio")->create();
        $now = Carbon::now()->toDatestring();
        $famiglia->assegnaComponente($persona, $famiglia::getCapoFamigliaEnum(), $now);
        $this->assertEquals($now, $famiglia->capofamiglia()->pivot->data_entrata);

        // test assegna MOGLIE
        $famiglia = factory(Famiglia::class)->create();
        $persona = factory(Persona::class)->states("maggiorenne", "femmina")->create();
        $now = Carbon::now()->toDatestring();
        $famiglia->assegnaComponente($persona, $famiglia::getMoglieEnum(), $now);
        $this->assertEquals($now, $famiglia->moglie()->pivot->data_entrata);
    }

    public function testAssegnaCapoFamigliaThrowExceptionsWithMinorenne()
    {
        $famiglia = factory(Famiglia::class)->create();
        $minorenne = factory(Persona::class)->states("minorenne", "maschio")->create();
        $now = Carbon::now()->toDatestring();
        $this->expectException(CouldNotAssignCapoFamiglia::class);
        $famiglia->assegnaCapoFamiglia($minorenne, $now);
    }

    public function testAssegnaCapoFamigliaThrowExceptionsWithMAlreadyCapofamiglia()
    {
        $famiglia = factory(Famiglia::class)->create();
        $capoFam = factory(Persona::class)->states("maggiorenne", "maschio")->create();
        $now = Carbon::now()->toDatestring();
        $famiglia->assegnaCapoFamiglia($capoFam, $now);
        $newCapoFam = factory(Persona::class)->states("maggiorenne", "maschio")->create();
        $this->expectException(CouldNotAssignCapoFamiglia::class);
        $famiglia->assegnaCapoFamiglia($newCapoFam, $now);
    }

    public function testAssegnaMoglieThrowErrorWithMultipleMogli()
    {
        $now = Carbon::now()->toDatestring();
        $famiglia = factory(Famiglia::class)->create();

        $persona = factory(Persona::class)->states("maggiorenne", "femmina")->create();
        $famiglia->assegnaMoglie($persona);
        $persona2 = factory(Persona::class)->states("maggiorenne", "femmina")->create();
        $this->expectException(CouldNotAssignMoglie::class);
        $famiglia->assegnaMoglie($persona2);
    }

    public function testAssegnaMoglieThrowErrorWithMan()
    {
        $famiglia = factory(Famiglia::class)->create();
        $persona = factory(Persona::class)->states("maggiorenne", "maschio")->create();
        $this->expectException(CouldNotAssignMoglie::class);
        $famiglia->assegnaMoglie($persona);
    }

    public function testAssegnaMoglieThrowErrorWithMinorenne()
    {
        $famiglia = factory(Famiglia::class)->create();

        $persona = factory(Persona::class)->states("minorenne", "maschio")->create();
        $this->expectException(CouldNotAssignMoglie::class);
        $famiglia->assegnaMoglie($persona);
    }

    public function testAssegnaMoglieThrowErrorWithSingle()
    {
        $famiglia = factory(Famiglia::class)->create();

        $persona = factory(Persona::class)->states("maggiorenne", "maschio")->create();
        $famiglia->assegnaSingle($persona);

        $persona = factory(Persona::class)->states("maggiorenne", "femmina")->create();
        $this->expectException(CouldNotAssignMoglie::class);
        $famiglia->assegnaMoglie($persona);
    }

    public function testAssegnaMoglie()
    {
        $famiglia = factory(Famiglia::class)->create();
        $persona = factory(Persona::class)->states("maggiorenne", "femmina")->create();
        $famiglia->assegnaMoglie($persona);
        $this->assertEquals($famiglia->moglie()->id, $persona->id);
        $this->assertEquals($famiglia->data_creazione, $famiglia->moglie()->pivot->data_entrata);

        //moglie
        $famiglia = factory(Famiglia::class)->create();
        $persona = factory(Persona::class)->states("maggiorenne", "femmina")->create();
        $now = Carbon::now()->toDatestring();
        $famiglia->assegnaMoglie($persona, $now);
        $this->assertEquals($famiglia->moglie()->id, $persona->id);
        $this->assertEquals($now, $famiglia->moglie()->pivot->data_entrata);
    }

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

        $this->assertEquals(4, $famiglia->componentiAttuali()->get()->count());

        // toglie un figlio dal nucleo familiare
        $famiglia->uscitaDalNucleoFamiliare($fnato, Carbon::now()->addYears(4)->toDatestring(),
            "test remove from nucleo");

        $this->assertEquals(3, $famiglia->componentiAttuali()->get()->count());
    }
    /**
     * Test se l'uscita dal nucleo familiare di un figlio.
     *
     * */
    public function testAssegnaNuovoGruppoFamiliare()
    {
        $famiglia = factory(Famiglia::class)->create();
        $capoFam = factory(Persona::class)->states("maggiorenne", "maschio")->create();
        $moglie = factory(Persona::class)->states("maggiorenne", "femmina")->create();
        $fnato = factory(Persona::class)->states("minorenne", "femmina")->create();
        $gruppo = GruppoFamiliare::all()->random();
        $now = Carbon::now()->toDatestring();
        $capoFam->entrataMaggiorenneSposato($now, $gruppo->id);
        $moglie->entrataMaggiorenneSposato($now, $gruppo->id);
        $famiglia->assegnaCapoFamiglia($capoFam);
        $famiglia->assegnaMoglie($moglie);
        $famiglia->assegnaFiglioNato($fnato);


//        $gruppo_attuale_id,
//        $dataUscitaGruppoFamiliareAttuale,
//        $gruppo_nuovo_id,
//        $data_entrata = null
        $nuovoGruppo = GruppoFamiliare::all()->random();
        $famiglia->assegnaFamigliaANuovoGruppoFamiliare($gruppo->id, Carbon::now()->toDatestring(), $nuovoGruppo->id, Carbon::now()->toDatestring());
        $this->assertEquals($nuovoGruppo->id, $capoFam->gruppofamiliareAttuale()->id);
        $this->assertEquals($nuovoGruppo->id, $moglie->gruppofamiliareAttuale()->id);
        $this->assertEquals($nuovoGruppo->id, $fnato->gruppofamiliareAttuale()->id);

    }
}
