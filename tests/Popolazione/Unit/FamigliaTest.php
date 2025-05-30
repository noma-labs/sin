<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Nomadelfia\Exceptions\CouldNotAssignCapoFamiglia;
use App\Nomadelfia\Exceptions\CouldNotAssignMoglie;
use App\Nomadelfia\Famiglia\Models\Famiglia;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataDallaNascitaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneAccoltoAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneConFamigliaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\UscitaPersonaAction;
use Carbon\Carbon;
use InvalidArgumentException;

it('throws and invalidArgument on assign a component', function (): void {
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia->assegnaComponente($persona, 'NOT EXISTING');
})->throws(InvalidArgumentException::class);

it('assign a component', function (): void {
    // assegna capo famiglia
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $now = Carbon::now()->startOfDay();
    $famiglia->assegnaComponente($persona, $famiglia::getCapoFamigliaEnum());
    expect($famiglia->capofamiglia()->id)->toBe($persona->id);

    // test assegna MOGLIE
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->femmina()->create();
    $now = Carbon::now()->startOfDay();
    $famiglia->assegnaComponente($persona, $famiglia::getMoglieEnum());
    expect($famiglia->moglie()->id)->toBe($persona->id);
});

it('throws exception with bad capo famiglia', function (): void {
    $famiglia = Famiglia::factory()->create();
    $minorenne = Persona::factory()->minorenne()->maschio()->create();
    $famiglia->assegnaCapoFamiglia($minorenne);
})->throws(CouldNotAssignCapoFamiglia::class);

it('throw exception with already capo famiglia', function (): void {
    $famiglia = Famiglia::factory()->create();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia->assegnaCapoFamiglia($capoFam);
    $newCapoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia->assegnaCapoFamiglia($newCapoFam);
})->throws(CouldNotAssignCapoFamiglia::class);

it('thexception with multiple wives', function (): void {
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->femmina()->create();
    $famiglia->assegnaMoglie($persona);
    $persona2 = Persona::factory()->maggiorenne()->femmina()->create();
    $famiglia->assegnaMoglie($persona2);
})->throws(CouldNotAssignMoglie::class);

it('thexception with a man', function (): void {
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $this->expectException(CouldNotAssignMoglie::class);
    $famiglia->assegnaMoglie($persona);
})->throws(CouldNotAssignMoglie::class);

it('thexception with minorenne', function (): void {
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia->assegnaMoglie($persona);
})->throws(CouldNotAssignMoglie::class);

it('assign a wife succesfully', function (): void {
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->femmina()->create();
    $famiglia->assegnaMoglie($persona);
    expect($persona->id)->toBe($famiglia->moglie()->id);

    // moglie
    $famiglia = Famiglia::factory()->create();
    $persona = Persona::factory()->maggiorenne()->femmina()->create();
    $now = Carbon::now()->startOfDay();
    $famiglia->assegnaMoglie($persona);
    expect($famiglia->moglie()->id)->toBe($persona->id);
});

/**
 * Test se l'uscita dal nucleo familiare di un figlio.
 *
 * */
it('exit a children from family', function (): void {
    $now = Carbon::now()->startOfDay();
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
    $famiglia->assegnaMoglie($moglie);

    $act = app(EntrataDallaNascitaAction::class);
    $act->execute($fnato, Famiglia::findOrFail($famiglia->id));

    $act = app(EntrataMinorenneAccoltoAction::class);
    $act->execute($faccolto, Carbon::now()->addYears(2)->startOfDay(), Famiglia::findOrFail($famiglia->id));

    expect($famiglia->componentiAttuali()->get()->count())->toBe(4);

    // toglie un figlio dal nucleo familiare
    $famiglia->uscitaDalNucleoFamiliare($fnato, 'test remove from nucleo');

    expect($famiglia->componentiAttuali()->get()->count())->toBe(3);
});

/**
 * Test se l'uscita dal nucleo familiare di un figlio.
 *
 * */
it('assign a new group succesfully', function (): void {
    $famiglia = Famiglia::factory()->create();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $moglie = Persona::factory()->maggiorenne()->femmina()->create();
    $fnato = Persona::factory()->minorenne()->femmina()->create();
    $gruppo = GruppoFamiliare::all()->random();
    $now = Carbon::now()->startOfDay();
    $act = app(EntrataMaggiorenneConFamigliaAction::class);
    $act->execute($capoFam, $now, $gruppo);
    $act = app(EntrataMaggiorenneConFamigliaAction::class);
    $act->execute($moglie, $now, $gruppo);
    $famiglia->assegnaCapoFamiglia($capoFam);
    $famiglia->assegnaMoglie($moglie);
    $famiglia->assegnaFiglioNato($fnato);

    $nuovoGruppo = GruppoFamiliare::all()->random();
    $famiglia->assegnaFamigliaANuovoGruppoFamiliare($gruppo->id, Carbon::now()->startOfDay(), $nuovoGruppo->id, Carbon::now()->startOfDay());
    expect($capoFam->gruppofamiliareAttuale()->id)->toBe($nuovoGruppo->id)
        ->and($moglie->gruppofamiliareAttuale()->id)->toBe($nuovoGruppo->id)
        ->and($fnato->gruppofamiliareAttuale()->id)->toBe($nuovoGruppo->id);

});

it('get famiglie numerose', function (): void {
    $famiglia = Famiglia::factory()->create();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $moglie = Persona::factory()->maggiorenne()->femmina()->create();
    $gruppo = GruppoFamiliare::all()->random();
    $now = Carbon::now()->startOfDay();
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

    app(UscitaPersonaAction::class)->execute($figlio, Carbon::now()->startOfDay());

    $fanNum = Famiglia::famiglieNumerose(8);
    expect($fanNum)->toHaveCount(count($beforeFanNum));
});
