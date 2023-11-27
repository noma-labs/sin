<?php

namespace Tests\Unit;

use App\Nomadelfia\Exceptions\CouldNotAssignCapoFamiglia;
use App\Nomadelfia\Exceptions\CouldNotAssignMoglie;
use Carbon;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataDallaNascitaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneAccoltoAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneConFamigliaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\UscitaPersonaAction;
use InvalidArgumentException;

it('throws and invalidArgument on assign a component', function () {
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia->assegnaComponente($persona, 'NOT EXISTING');
})->throws(InvalidArgumentException::class);

it('assign a component', function () {
    // assegna capo famiglia
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $now = Carbon::now()->toDatestring();
    $famiglia->assegnaComponente($persona, $famiglia::getCapoFamigliaEnum());
    expect($famiglia->capofamiglia()->id)->toBe($persona->id);

    // test assegna MOGLIE
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->femmina()->create();
    $now = Carbon::now()->toDatestring();
    $famiglia->assegnaComponente($persona, $famiglia::getMoglieEnum());
    expect($famiglia->moglie()->id)->toBe($persona->id);
});

it('throws and expection with bad capo famiglia', function () {
    $famiglia = Famiglia::factory()->create();
    $minorenne = Persona::factory()->minorenne()->maschio()->create();
    $famiglia->assegnaCapoFamiglia($minorenne);
})->throws(CouldNotAssignCapoFamiglia::class);

it('throw exceptions  with already capo famiglia', function () {
    $famiglia = Famiglia::factory()->create();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia->assegnaCapoFamiglia($capoFam);
    $newCapoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia->assegnaCapoFamiglia($newCapoFam);
})->throws(CouldNotAssignCapoFamiglia::class);

it('throw expection with multiple mogli', function () {
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->femmina()->create();
    $famiglia->assegnaMoglie($persona);
    $persona2 = Persona::factory()->maggiorenne()->femmina()->create();
    $famiglia->assegnaMoglie($persona2);
})->throws(CouldNotAssignMoglie::class);

it('throw expection with a man', function () {
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $this->expectException(CouldNotAssignMoglie::class);
    $famiglia->assegnaMoglie($persona);
})->throws(CouldNotAssignMoglie::class);

it('throw expection with minorenne', function () {
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia->assegnaMoglie($persona);
})->throws(CouldNotAssignMoglie::class);


it('assign a wife succesfully', function () {
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->femmina()->create();
    $famiglia->assegnaMoglie($persona);
    expect($persona->id)->toBe($famiglia->moglie()->id);

    //moglie
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->femmina()->create();
    $now = Carbon::now()->toDatestring();
    $famiglia->assegnaMoglie($persona, $now);
    expect($famiglia->moglie()->id)->toBe($persona->id);
});

/**
 * Test se l'uscita dal nucleo familiare di un figlio.
 *
 * */
it('exit a children from family', function () {
    $now = Carbon::now()->toDatestring();
    $gruppo = GruppoFamiliare::all()->random();

    $famiglia = Famiglia::factory()->create();

    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $moglie = Persona::factory()->maggiorenne()->femmina()->create();
    $fnato = Persona::factory()->minorenne()->femmina()->create();
    $faccolto = Persona::factory()->minorenne()->maschio()->create();

    $act = app(EntrataMaggiorenneConFamigliaAction::class);
    $act->execute($capoFam, $now, $gruppo);
    $act = app(EntrataMaggiorenneConFamigliaAction::class);
    $act->execute($moglie, $now, $gruppo);
    $famiglia->assegnaCapoFamiglia($capoFam);
    $famiglia->assegnaMoglie($moglie, $now);

    $act = app(EntrataDallaNascitaAction::class);
    $act->execute($fnato, Famiglia::findOrFail($famiglia->id));

    $act = app(EntrataMinorenneAccoltoAction::class);
    $act->execute($faccolto, Carbon::now()->addYears(2)->toDatestring(), Famiglia::findOrFail($famiglia->id));

    expect($famiglia->componentiAttuali()->get()->count())->toBe(4);

    // toglie un figlio dal nucleo familiare
    $famiglia->uscitaDalNucleoFamiliare($fnato, Carbon::now()->addYears(4)->toDatestring(),
        'test remove from nucleo');

    expect($famiglia->componentiAttuali()->get()->count())->toBe(3);
});

/**
 * Test se l'uscita dal nucleo familiare di un figlio.
 *
 * */
it('assign a new group succesfully', function () {
    $famiglia = Famiglia::factory()->create();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $moglie = Persona::factory()->maggiorenne()->femmina()->create();
    $fnato = Persona::factory()->minorenne()->femmina()->create();
    $gruppo = GruppoFamiliare::all()->random();
    $now = Carbon::now()->toDatestring();
    $act = app(EntrataMaggiorenneConFamigliaAction::class);
    $act->execute($capoFam, $now, $gruppo);
    $act = app(EntrataMaggiorenneConFamigliaAction::class);
    $act->execute($moglie, $now, $gruppo);
    $famiglia->assegnaCapoFamiglia($capoFam);
    $famiglia->assegnaMoglie($moglie);
    $famiglia->assegnaFiglioNato($fnato);

    $nuovoGruppo = GruppoFamiliare::all()->random();
    $famiglia->assegnaFamigliaANuovoGruppoFamiliare($gruppo->id, Carbon::now()->toDatestring(), $nuovoGruppo->id, Carbon::now()->toDatestring());
    expect($capoFam->gruppofamiliareAttuale()->id)->toBe($nuovoGruppo->id)
        ->and($moglie->gruppofamiliareAttuale()->id)->toBe($nuovoGruppo->id)
        ->and($fnato->gruppofamiliareAttuale()->id)->toBe($nuovoGruppo->id);

});

it('get famiglie numerose', function () {
    $famiglia = Famiglia::factory()->create();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $moglie = Persona::factory()->maggiorenne()->femmina()->create();
    $gruppo = GruppoFamiliare::all()->random();
    $now = Carbon::now()->toDatestring();
    app(EntrataMaggiorenneConFamigliaAction::class)->execute($capoFam, $now, $gruppo);
    app(EntrataMaggiorenneConFamigliaAction::class)->execute($moglie, $now, $gruppo);
    $famiglia->assegnaCapoFamiglia($capoFam);
    $famiglia->assegnaMoglie($moglie);

    $beforeFanNum = Famiglia::famiglieNumerose(8);
    $figlio = Persona::factory()->minorenne()->femmina()->create();
    app(EntrataMinorenneConFamigliaAction::class)->execute($figlio, $now, $famiglia);
    app(EntrataMinorenneConFamigliaAction::class)->execute(Persona::factory()->minorenne()->femmina()->create(), $now, $famiglia);
    app(EntrataMinorenneConFamigliaAction::class)->execute(Persona::factory()->minorenne()->femmina()->create(), $now, $famiglia);
    app(EntrataMinorenneConFamigliaAction::class)->execute(Persona::factory()->minorenne()->femmina()->create(), $now, $famiglia);
    app(EntrataMinorenneConFamigliaAction::class)->execute(Persona::factory()->minorenne()->femmina()->create(), $now, $famiglia);
    app(EntrataMinorenneConFamigliaAction::class)->execute(Persona::factory()->minorenne()->femmina()->create(), $now, $famiglia);

    $fanNum6 = Famiglia::famiglieNumerose(8);
    expect($fanNum6)->toHaveCount(count($beforeFanNum) + 1);

    app(UscitaPersonaAction::class)->execute($figlio, Carbon::now()->toDatestring());

    $fanNum = Famiglia::famiglieNumerose(8);
    expect($fanNum)->toHaveCount(count($beforeFanNum));
});
