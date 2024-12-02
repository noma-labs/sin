<?php

namespace Tests\Popolazione\Unit;

use App\Scuola\Models\Anno;
use App\Scuola\Models\ClasseTipo;
use Carbon\Carbon;
use Domain\Nomadelfia\Azienda\Models\Azienda;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Incarico\Models\Incarico;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\AssegnaAziendaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\AssegnaGruppoFamiliareAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\AssegnaIncaricoAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\DecessoPersonaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataDallaNascitaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneAccoltoAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\UscitaFamigliaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\UscitaPersonaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;

it('remove dead person from population', function (): void {
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $data_entrata = Carbon::now()->toDatestring();
    $gruppo = GruppoFamiliare::all()->random();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));

    $tot = PopolazioneNomadelfia::totalePopolazione();
    $pop = PopolazioneNomadelfia::popolazione();
    $this->assertEquals($tot, count($pop));

    $data_decesso = Carbon::now()->addYears(5)->toDatestring();

    $action = app(DecessoPersonaAction::class);
    $action->execute($persona, $data_decesso);

    $persona = Persona::findOrFail($persona->id);
    expect($persona->isDeceduta())->toBeTrue()
        ->and($persona->isPersonaInterna())->toBeFalse()
        ->and(PopolazioneNomadelfia::totalePopolazione())->toBe($tot - 1);
    $this->assertNull($persona->posizioneAttuale());
    expect($persona->posizioniStorico()->get()->last()->pivot->data_fine)->toBe($data_decesso);
    $this->assertNull($persona->statoAttuale());
    expect($persona->statiStorico()->get()->last()->pivot->data_fine)->toBe($data_decesso);
    $this->assertNull($persona->gruppofamiliareAttuale());
    expect($persona->gruppofamiliariStorico()->get()->last()->pivot->data_uscita_gruppo)->toBe($data_decesso)
        ->and($persona->famigliaAttuale())->toBeNull();

    $pop = PopolazioneNomadelfia::popolazione();
    expect(count($pop))->toBe($tot - 1);
});

it('manage exit of an adult', function (): void {
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $data_entrata = Carbon::now()->toDatestring();
    $gruppo = GruppoFamiliare::all()->random();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));

    $azienda = Azienda::factory()->create();
    // $persona->assegnaLavoratoreAzienda($azienda, $data_entrata);
    app(AssegnaAziendaAction::class)->execute($persona, $azienda, Carbon::now(), 'LAVORATORE');
    expect($persona->aziendeAttuali()->count())->toBe(1);
    // assegna incarico
    $incarico = Incarico::factory()->create();
    $action = new AssegnaIncaricoAction;
    $action->execute($persona, $incarico, Carbon::now());

    expect($incarico->lavoratoriAttuali()->count())->toBe(1);

    $tot = PopolazioneNomadelfia::totalePopolazione();
    $pop = PopolazioneNomadelfia::popolazione();
    expect(count($pop))->toBe($tot);

    $data_uscita = Carbon::now()->addYears(5)->toDatestring();
    $action = app(UscitaPersonaAction::class);
    $action->execute($persona, $data_uscita);

    $this->assertFalse($persona->isPersonaInterna());
    expect(PopolazioneNomadelfia::totalePopolazione())->toBe($tot - 1);
    $this->assertNull($persona->posizioneAttuale());
    $last_posi = $persona->posizioniStorico()->get()->last();
    expect($last_posi->pivot->data_fine)->toBe($data_uscita);
    $celibe = Stato::perNome('celibe');
    expect($persona->statoAttuale()->id)->toBe($celibe->id)
        ->and($persona->gruppofamiliareAttuale())->toBeNull()
        ->and($persona->gruppofamiliariStorico()->get()->last()->pivot->data_uscita_gruppo)->toBe($data_uscita)
        ->and($persona->famigliaAttuale())->toBeNull()
        ->and($persona->aziendeAttuali()->count())->toBe(0)
        ->and($azienda->lavoratoriStorici()->count())->toBe(1)
        ->and($incarico->lavoratoriAttuali()->count())->toBe(0)
        ->and($incarico->lavoratoriStorici()->count())->toBe(1);

    $pop = PopolazioneNomadelfia::popolazione();
    expect(count($pop))->toBe($tot - 1);
});

/*
* Testa che quando figlio minorenne esce da nomadelfia,
* viene tolto da tutte le posizioni con la data di uscita
* e viene tolto dal nucleo familiare.
*/
it('manage exit of underage', function (): void {
    $persona = Persona::factory()->minorenne()->maschio()->create();

    $gruppo = GruppoFamiliare::all()->random();

    $famiglia = Famiglia::factory()->create();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $capoFam->gruppifamiliari()->attach($gruppo->id,
        ['stato' => '1', 'data_entrata_gruppo' => Carbon::now()->subYears(10)->toDatestring()]);
    $famiglia->componenti()->attach($capoFam->id,
        ['stato' => '1', 'posizione_famiglia' => 'CAPO FAMIGLIA']);

    $act = app(EntrataDallaNascitaAction::class);
    $act->execute($persona, Famiglia::findOrFail($famiglia->id));

    // assegna minorenne in una classe
    $a = Anno::createAnno(2100);
    $classe = $a->aggiungiClasse(ClasseTipo::all()->random());
    $classe->aggiungiAlunno($persona, Carbon::now());
    expect($classe->alunni()->count())->toBe(1);

    $tot = PopolazioneNomadelfia::totalePopolazione();
    $pop = PopolazioneNomadelfia::popolazione();
    expect($tot)->toBe(count($pop));

    $data_uscita = Carbon::now()->addYears(5)->toDatestring();

    $act = app(UscitaPersonaAction::class);
    $act->execute($persona, $data_uscita, true);

    expect(PopolazioneNomadelfia::totalePopolazione())->toBe($tot - 1);
    $this->assertNull($persona->posizioneAttuale());
    $last_posi = $persona->posizioniStorico()->get()->last();
    expect($last_posi->pivot->data_inizio)->toBe($persona->data_nascita);
    expect($last_posi->pivot->data_fine)->toBe($data_uscita);
    $celibe = Stato::perNome('celibe');
    expect($persona->statoAttuale()->id)->toBe($celibe->id)
        ->and($persona->gruppofamiliareAttuale())->toBeNull()
        ->and($persona->gruppofamiliariStorico()->get()->last()->pivot->data_uscita_gruppo)->toBe($data_uscita)
        ->and($persona->famigliaAttuale())->toBeNull()
        ->and($persona->famiglieStorico()->get()->last()->id)->toBe($famiglia->id)
        ->and($classe->alunniAttuali()->count())->toBe(0);

    $pop = PopolazioneNomadelfia::popolazione();
    expect(count($pop))->toBe($tot - 1);
});

it('manage exit of family', function (): void {
    $init_tot = PopolazioneNomadelfia::totalePopolazione();
    $pop = PopolazioneNomadelfia::popolazione();
    expect(count($pop))->toBe($init_tot);

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
    $act->execute($faccolto, Carbon::now()->addYears(2)->toDatestring(), $famiglia);

    expect(PopolazioneNomadelfia::totalePopolazione())->toBe($init_tot + 4);
    $pop = PopolazioneNomadelfia::popolazione();
    expect(count($pop))->toBe($init_tot + 4);

    $data_uscita = Carbon::now()->toDatestring();
    $action = app(UscitaFamigliaAction::class);
    $action->execute($famiglia, $data_uscita);

    expect(PopolazioneNomadelfia::totalePopolazione())->toBe($init_tot);
    $pop = PopolazioneNomadelfia::popolazione();
    expect(count($pop))->toBe($init_tot);
});

/*
* Testa l'uscita di una famiglia con alcuni componeneti fuori dal nucleo.
* Controlla che i componenti fuori dal nucleo rimagono come persone interne.
*/
it('manage people not part of family when it exits', function (): void {
    $init_tot = PopolazioneNomadelfia::totalePopolazione();
    $pop = PopolazioneNomadelfia::popolazione();
    $this->assertEquals($init_tot, count($pop));

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
    $act->execute($faccolto, Carbon::now()->addYears(2)->toDatestring(), $famiglia);

    expect(PopolazioneNomadelfia::totalePopolazione())->toBe($init_tot + 4);
    $pop = PopolazioneNomadelfia::popolazione();
    expect(count($pop))->toBe($init_tot + 4);

    // toglie un figlio dal nucleo familiare
    $famiglia->uscitaDalNucleoFamiliare($fnato, Carbon::now()->addYears(4)->toDatestring(),
        ' remove from nucleo');

    $data_uscita = Carbon::now()->toDatestring();
    $action = app(UscitaFamigliaAction::class);
    $action->execute($famiglia, $data_uscita);

    // controlla che il figlio fuori dal nucleo non è uscito
    expect(PopolazioneNomadelfia::totalePopolazione())->toBe($init_tot + 1);
    $pop = PopolazioneNomadelfia::popolazione();
    expect(count($pop))->toBe($init_tot + 1);
});

/*
* Testa il conteggio dei figli minorenni nella popolazione
*/
it('count the underages of the population', function (): void {
    $famiglia = Famiglia::factory()->create();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia->assegnaCapoFamiglia($capoFam);
    $gruppo = GruppoFamiliare::all()->random();

    $action = app(AssegnaGruppoFamiliareAction::class);
    $action->execute($capoFam, $gruppo, Carbon::now());

    $tot = PopolazioneNomadelfia::totalePopolazione();
    $min = PopolazioneNomadelfia::figliDaEta(0, 18, 'nominativo', null)->count();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $act = app(EntrataDallaNascitaAction::class);
    $act->execute($persona, Famiglia::findOrFail($famiglia->id));

    expect(PopolazioneNomadelfia::totalePopolazione())->toBe($tot + 1);
    expect(PopolazioneNomadelfia::figliDaEta(0, 18, 'nominativo', null)->count())->toBe($min + 1);

    $mag = PopolazioneNomadelfia::figliDaEta(18, null, 'nominativo', null)->count();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $act = app(EntrataDallaNascitaAction::class);
    $act->execute($persona, Famiglia::findOrFail($famiglia->id));
    expect(PopolazioneNomadelfia::totalePopolazione())->toBe($tot + 2)
        ->and(PopolazioneNomadelfia::figliDaEta(0, 18, 'nominativo', null)->count())->toBe($min + 1)
        ->and(PopolazioneNomadelfia::figliDaEta(18, null, 'nominativo', null)->count())->toBe($mag + 1);
});

/*
 * Testa quando una persona diventa postulante e nomadelfo effettivo
 */
it('assign postulante and effettivo status', function (): void {
    // entrata maggiorenne
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::first();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));
    $now = Carbon::now()->subYears(4);
    $persona->assegnaPosizione(Posizione::perNome('postulante'), $now);
    expect($persona->posizioneAttuale()->isPostulante())->toBeTrue();

    $persona->assegnaPosizione(Posizione::perNome('effettivo'), $now->subYears(1));
    expect($persona->posizioneAttuale()->isEffettivo())->toBeTrue();
});

it('returns the figli between two ages', function (): void {
    // store the actual figli (maybe inserted by other tests)
    $before3 = PopolazioneNomadelfia::figliDaEta(3, 4, 'nominativo', null)->count();
    $before24 = PopolazioneNomadelfia::figliDaEta(2, 4, 'nominativo', null)->count();

    $famiglia = Famiglia::factory()->create();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia->assegnaCapoFamiglia($capoFam);

    $action = app(AssegnaGruppoFamiliareAction::class);
    $action->execute($capoFam, GruppoFamiliare::all()->random(), Carbon::now());

    $p1 = Persona::factory()->create(['data_nascita' => Carbon::now()->subYears(3)->startOfYear()]); // 2018-01-01 00:00:00
    $p0 = Persona::factory()->create(['data_nascita' => Carbon::now()->subYears(3)]);                // 2018-now()
    $p2 = Persona::factory()->create(['data_nascita' => Carbon::now()->subYears(3)->endOfYear()]);   // 2018-12-31 23:59:59

    $pafter = Persona::factory()->create(['data_nascita' => Carbon::now()->subYears(2)->startOfYear()]);   // 2019-01-01 00:00:00

    app(EntrataDallaNascitaAction::class)->execute($p0, $famiglia);
    app(EntrataDallaNascitaAction::class)->execute($p1, $famiglia);
    app(EntrataDallaNascitaAction::class)->execute($p2, $famiglia);
    app(EntrataDallaNascitaAction::class)->execute($pafter, $famiglia);

    expect(count(PopolazioneNomadelfia::figliDaEta(3, 4, 'nominativo', null, true)))->toBe($before3 + 3)
        ->and(count(PopolazioneNomadelfia::figliDaEta(3, 4, 'nominativo', null, false)))->toBe($before3 + 2)
        ->and(count(PopolazioneNomadelfia::figliDaEta(2, 4, 'nominativo', null, false)))->toBe($before24 + 4)
        ->and(count(PopolazioneNomadelfia::figliDaEta(2, 4, 'nominativo', null, true)))->toBe($before24 + 4);
});

it('return the count of population', function (): void {
    $before = PopolazioneNomadelfia::presente()->count();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $data_entrata = Carbon::now()->toDatestring();
    $gruppo = GruppoFamiliare::all()->random();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));

    $after = PopolazioneNomadelfia::presente()->count();
    expect($after - $before)->toBe(1);

    $c = PopolazioneNomadelfia::presente()->where('id', '=', $persona->id)->count();
    expect($c)->toBe(1);
    $c = PopolazioneNomadelfia::presente()->where('nominativo', '=', $persona->nominativo)->count();
    expect($c)->toBe(1);

    $action = app(UscitaPersonaAction::class);
    $action->execute($persona, Carbon::now()->toDatestring());
    $afterUscita = PopolazioneNomadelfia::presente()->count();
    expect($before - $afterUscita)->toBe(0);
});

it('get the people present at a specific date', function (): void {
    $now = Carbon::now();
    $before = PopolazioneNomadelfia::presentAt($now)->count();

    // persone enter after and exited after is NOT present
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, $now->copy()->addMonth(), GruppoFamiliare::all()->random());
    app(UscitaPersonaAction::class)->execute($persona, $now->copy()->addMonth(2)->toDateString());
    $after = PopolazioneNomadelfia::presentAt($now)->count();
    expect($after - $before)->toBe(0);

    // persone enter before and exited before is NOT present
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, $now->copy()->subYear(), GruppoFamiliare::all()->random());
    app(UscitaPersonaAction::class)->execute($persona, $now->copy()->subMonth()->toDateString());
    $after = PopolazioneNomadelfia::presentAt($now)->count();
    expect($after - $before)->toBe(0);

    // persone enter before and exited after is present
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, $now->copy()->subYear(), GruppoFamiliare::all()->random());
    app(UscitaPersonaAction::class)->execute($persona, $now->copy()->addMonth()->toDateString());
    $after = PopolazioneNomadelfia::presentAt($now)->count();
    expect($after - $before)->toBe(1);

    // persone never exited is present
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, $now->copy()->subMonth(), GruppoFamiliare::all()->random());
    $after = PopolazioneNomadelfia::presentAt($now)->count();
    expect($after - $before)->toBe(2);
});
