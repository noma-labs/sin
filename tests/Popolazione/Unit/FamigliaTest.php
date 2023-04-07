<?php

namespace Tests\Unit;

use App\Nomadelfia\Exceptions\CouldNotAssignCapoFamiglia;
use App\Nomadelfia\Exceptions\CouldNotAssignMoglie;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataDallaNascitaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneConFamigliaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\SaveEntrataInNomadelfiaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneAccoltoAction;
use Domain\Nomadelfia\Persona\Models\Persona;
use InvalidArgumentException;
use Tests\CreatesApplication;
use Tests\MigrateFreshDB;
use Tests\TestCase;
use Carbon;


it("throws and error on assign a component", function () {
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $this->expectException(InvalidArgumentException::class);
    $famiglia->assegnaComponente($persona, "NOT EXISTING", Carbon::now()->toDatestring());
    $this->assertEquals(3, 3);
});


it("assign a component", function () {
    // assegna capo famiglia
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $now = Carbon::now()->toDatestring();
    $famiglia->assegnaComponente($persona, $famiglia::getCapoFamigliaEnum(), $now);
    $this->assertEquals($now, $famiglia->capofamiglia()->pivot->data_entrata);

    // test assegna MOGLIE
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->femmina()->create();
    $now = Carbon::now()->toDatestring();
    $famiglia->assegnaComponente($persona, $famiglia::getMoglieEnum(), $now);
    $this->assertEquals($now, $famiglia->moglie()->pivot->data_entrata);
});

it("throws and error with capo famiglia", function () {
    $famiglia = Famiglia::factory()->create();
    $minorenne = Persona::factory()->minorenne()->maschio()->create();
    $now = Carbon::now()->toDatestring();
    $this->expectException(CouldNotAssignCapoFamiglia::class);
    $famiglia->assegnaCapoFamiglia($minorenne, $now);
});

it("testAssegnaCapoFamigliaThrowExceptionsWithMAlreadyCapofamiglia", function () {
    $famiglia = Famiglia::factory()->create();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $now = Carbon::now()->toDatestring();
    $famiglia->assegnaCapoFamiglia($capoFam, $now);
    $newCapoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $this->expectException(CouldNotAssignCapoFamiglia::class);
    $famiglia->assegnaCapoFamiglia($newCapoFam, $now);
});

it("testAssegnaMoglieThrowErrorWithMultipleMogli", function () {
    $famiglia = Famiglia::factory()->create();

    $persona = Persona::factory()->maggiorenne()->femmina()->create();
    $famiglia->assegnaMoglie($persona);
    $persona2 = Persona::factory()->maggiorenne()->femmina()->create();
    $this->expectException(CouldNotAssignMoglie::class);
    $famiglia->assegnaMoglie($persona2);
});

it("testAssegnaMoglieThrowErrorWithMan", function () {
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $this->expectException(CouldNotAssignMoglie::class);
    $famiglia->assegnaMoglie($persona);
});

it("testAssegnaMoglieThrowErrorWithMinorenne", function () {
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();;
    $this->expectException(CouldNotAssignMoglie::class);
    $famiglia->assegnaMoglie($persona);
});

it("testAssegnaMoglieThrowErrorWithSingle", function () {
    $famiglia = Famiglia::factory()->create();

    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia->assegnaSingle($persona);

    $persona = Persona::factory()->maggiorenne()->femmina()->create();
    $this->expectException(CouldNotAssignMoglie::class);
    $famiglia->assegnaMoglie($persona);
});

it("testAssegnaMoglie", function () {
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->femmina()->create();
    $famiglia->assegnaMoglie($persona);
    $this->assertEquals($famiglia->moglie()->id, $persona->id);
    $this->assertEquals($famiglia->data_creazione, $famiglia->moglie()->pivot->data_entrata);

    //moglie
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->femmina()->create();
    $now = Carbon::now()->toDatestring();
    $famiglia->assegnaMoglie($persona, $now);
    $this->assertEquals($famiglia->moglie()->id, $persona->id);
    $this->assertEquals($now, $famiglia->moglie()->pivot->data_entrata);
});

/**
 * Test se l'uscita dal nucleo familiare di un figlio.
 *
 * */
it("testUscitaFiglioDalNucleo", function () {
    $now = Carbon::now()->toDatestring();
    $gruppo = GruppoFamiliare::all()->random();

    $famiglia = Famiglia::factory()->create();

    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $moglie = Persona::factory()->maggiorenne()->femmina()->create();
    $fnato = Persona::factory()->minorenne()->femmina()->create();
    $faccolto = Persona::factory()->minorenne()->maschio()->create();

    $act = new  EntrataMaggiorenneConFamigliaAction(new SaveEntrataInNomadelfiaAction());
    $act->execute($capoFam, $now, $gruppo);
    $act = new  EntrataMaggiorenneConFamigliaAction(new SaveEntrataInNomadelfiaAction());
    $act->execute($moglie, $now, $gruppo);
    $famiglia->assegnaCapoFamiglia($capoFam, $now);
    $famiglia->assegnaMoglie($moglie, $now);

    $act = new EntrataDallaNascitaAction(new SaveEntrataInNomadelfiaAction());
    $act->execute($fnato, Famiglia::findOrFail($famiglia->id));

    $act = new EntrataMinorenneAccoltoAction(new SaveEntrataInNomadelfiaAction());
    $act->execute($faccolto, Carbon::now()->addYears(2)->toDatestring(), Famiglia::findOrFail($famiglia->id));

    $this->assertEquals(4, $famiglia->componentiAttuali()->get()->count());

    // toglie un figlio dal nucleo familiare
    $famiglia->uscitaDalNucleoFamiliare($fnato, Carbon::now()->addYears(4)->toDatestring(),
        "test remove from nucleo");

    $this->assertEquals(3, $famiglia->componentiAttuali()->get()->count());
});

/**
 * Test se l'uscita dal nucleo familiare di un figlio.
 *
 * */
it("testAssegnaNuovoGruppoFamiliare", function () {
    $famiglia = Famiglia::factory()->create();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $moglie = Persona::factory()->maggiorenne()->femmina()->create();
    $fnato = Persona::factory()->minorenne()->femmina()->create();
    $gruppo = GruppoFamiliare::all()->random();
    $now = Carbon::now()->toDatestring();
    $act = new  EntrataMaggiorenneConFamigliaAction(new SaveEntrataInNomadelfiaAction());
    $act->execute($capoFam, $now, $gruppo);
    $act = new  EntrataMaggiorenneConFamigliaAction(new SaveEntrataInNomadelfiaAction());
    $act->execute($moglie, $now, $gruppo);
    $famiglia->assegnaCapoFamiglia($capoFam);
    $famiglia->assegnaMoglie($moglie);
    $famiglia->assegnaFiglioNato($fnato);

    $nuovoGruppo = GruppoFamiliare::all()->random();
    $famiglia->assegnaFamigliaANuovoGruppoFamiliare($gruppo->id, Carbon::now()->toDatestring(), $nuovoGruppo->id, Carbon::now()->toDatestring());
    $this->assertEquals($nuovoGruppo->id, $capoFam->gruppofamiliareAttuale()->id);
    $this->assertEquals($nuovoGruppo->id, $moglie->gruppofamiliareAttuale()->id);
    $this->assertEquals($nuovoGruppo->id, $fnato->gruppofamiliareAttuale()->id);

});

it("testFamiglieNumerose", function () {
    $famiglia = Famiglia::factory()->create();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $moglie = Persona::factory()->maggiorenne()->femmina()->create();
    $gruppo = GruppoFamiliare::all()->random();
    $now = Carbon::now()->toDatestring();
    $act = new  EntrataMaggiorenneConFamigliaAction(new SaveEntrataInNomadelfiaAction());
    $act->execute($capoFam, $now, $gruppo);
    $act = new  EntrataMaggiorenneConFamigliaAction(new SaveEntrataInNomadelfiaAction());
    $act->execute($moglie, $now, $gruppo);
    $famiglia->assegnaCapoFamiglia($capoFam);
    $famiglia->assegnaMoglie($moglie);
    $figlio = Persona::factory()->minorenne()->femmina()->create();
    $fam = Famiglia::find($famiglia->id);
    $act = new  EntrataMinorenneConFamigliaAction(new SaveEntrataInNomadelfiaAction());
    $act->execute($figlio, $now, $fam);
    $figlio = Persona::factory()->minorenne()->femmina()->create();
    $act = new  EntrataMinorenneConFamigliaAction(new SaveEntrataInNomadelfiaAction());
    $act->execute($figlio, $now, $fam);
    $figlio = Persona::factory()->minorenne()->femmina()->create();
    $act = new  EntrataMinorenneConFamigliaAction(new SaveEntrataInNomadelfiaAction());
    $act->execute($figlio, $now, $fam);
    $figlio = Persona::factory()->minorenne()->femmina()->create();
    $act = new  EntrataMinorenneConFamigliaAction(new SaveEntrataInNomadelfiaAction());
    $act->execute($figlio, $now, $fam);
    $figlio = Persona::factory()->minorenne()->femmina()->create();
    $act = new  EntrataMinorenneConFamigliaAction(new SaveEntrataInNomadelfiaAction());
    $act->execute($figlio, $now, $famiglia);
    $figlio = Persona::factory()->minorenne()->femmina()->create();
    $act = new  EntrataMinorenneConFamigliaAction(new SaveEntrataInNomadelfiaAction());
    $act->execute($figlio, $now, $famiglia);


    $fanNum = Famiglia::famiglieNumerose(10);
    $this->assertCount(0, $fanNum);
    $fanNum6 = Famiglia::famiglieNumerose(6);
    $this->assertCount(1, $fanNum6);
    $this->assertEquals($famiglia->id, $fanNum6[0]->id);
    $this->assertEquals(8, $fanNum6[0]->componenti);

    $figlio->uscita(Carbon::now()->toDatestring());
    $fanNum = Famiglia::famiglieNumerose(7);
    $this->assertCount(1, $fanNum);
    $this->assertEquals($famiglia->id, $fanNum[0]->id);
    $this->assertEquals(7, $fanNum[0]->componenti);
});

