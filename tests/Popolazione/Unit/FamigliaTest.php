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


it("throws and invalidArgument on assign a component", function () {
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia->assegnaComponente($persona, "NOT EXISTING", Carbon::now()->toDatestring());
})->throws(InvalidArgumentException::class);


it("assign a component", function () {
    // assegna capo famiglia
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $now = Carbon::now()->toDatestring();
    $famiglia->assegnaComponente($persona, $famiglia::getCapoFamigliaEnum(), $now);
    expect($famiglia->capofamiglia()->pivot->data_entrata)->toBe($now);

    // test assegna MOGLIE
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->femmina()->create();
    $now = Carbon::now()->toDatestring();
    $famiglia->assegnaComponente($persona, $famiglia::getMoglieEnum(), $now);
    expect($famiglia->moglie()->pivot->data_entrata)->toBe($now);
});

it("throws and expection with bad capo famiglia", function () {
    $famiglia = Famiglia::factory()->create();
    $minorenne = Persona::factory()->minorenne()->maschio()->create();
    $now = Carbon::now()->toDatestring();
    $famiglia->assegnaCapoFamiglia($minorenne, $now);
})->throws(CouldNotAssignCapoFamiglia::class);

it("throw exceptions  with already capo famiglia", function () {
    $famiglia = Famiglia::factory()->create();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $now = Carbon::now()->toDatestring();
    $famiglia->assegnaCapoFamiglia($capoFam, $now);
    $newCapoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia->assegnaCapoFamiglia($newCapoFam, $now);
})->throws(CouldNotAssignCapoFamiglia::class);

it("throw expection with multiple mogli", function () {
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->femmina()->create();
    $famiglia->assegnaMoglie($persona);
    $persona2 = Persona::factory()->maggiorenne()->femmina()->create();
    $famiglia->assegnaMoglie($persona2);
})->throws(CouldNotAssignMoglie::class);

it("throw expection with a man", function () {
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $this->expectException(CouldNotAssignMoglie::class);
    $famiglia->assegnaMoglie($persona);
})->throws(CouldNotAssignMoglie::class);

it("throw expection with minorenne", function () {
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();;
    $famiglia->assegnaMoglie($persona);
})->throws(CouldNotAssignMoglie::class);

it("throw expection with single", function () {
    $famiglia = Famiglia::factory()->create();

    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia->assegnaSingle($persona);

    $persona = Persona::factory()->maggiorenne()->femmina()->create();
    $famiglia->assegnaMoglie($persona);
})->throws(CouldNotAssignMoglie::class);

it("assign a wife succesfully", function () {
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->femmina()->create();
    $famiglia->assegnaMoglie($persona);
    expect($persona->id)->toBe($famiglia->moglie()->id);
    expect($famiglia->data_creazione)->toBe($famiglia->moglie()->pivot->data_entrata);

    //moglie
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->femmina()->create();
    $now = Carbon::now()->toDatestring();
    $famiglia->assegnaMoglie($persona, $now);
    expect($famiglia->moglie()->id)->toBe($persona->id);
    expect($now)->toBe($famiglia->moglie()->pivot->data_entrata);
});

/**
 * Test se l'uscita dal nucleo familiare di un figlio.
 *
 * */
it("exit a children from family", function () {
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

    expect($famiglia->componentiAttuali()->get()->count())->toBe(4);

    // toglie un figlio dal nucleo familiare
    $famiglia->uscitaDalNucleoFamiliare($fnato, Carbon::now()->addYears(4)->toDatestring(),
        "test remove from nucleo");

    expect($famiglia->componentiAttuali()->get()->count())->toBe(3);
});

/**
 * Test se l'uscita dal nucleo familiare di un figlio.
 *
 * */
it("assign a new group succesfully", function () {
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
    expect($capoFam->gruppofamiliareAttuale()->id)->toBe($nuovoGruppo->id);
    expect($moglie->gruppofamiliareAttuale()->id)->toBe($nuovoGruppo->id);
    expect($fnato->gruppofamiliareAttuale()->id)->toBe($nuovoGruppo->id);

});

it("get famiglie numerose", function () {
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
    expect($fanNum)->toBeEmpty();
    $fanNum6 = Famiglia::famiglieNumerose(6);
    expect($fanNum6)->toHaveCount(1);
    expect($fanNum6[0]->id)->toBe($famiglia->id);
    expect($fanNum6[0]->componenti)->toBe(8);

    $figlio->uscita(Carbon::now()->toDatestring());
    $fanNum = Famiglia::famiglieNumerose(7);
    expect($fanNum)->toHaveCount(1);
    expect($fanNum[0]->id)->toBe($famiglia->id);
    expect($fanNum[0]->componenti)->toBe(7);
});

