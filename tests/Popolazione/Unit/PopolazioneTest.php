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
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataDallaNascitaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneAccoltoAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\UscitaDaNomadelfiaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;

it('remove dead person from population', function () {
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $data_entrata = Carbon::now()->toDatestring();
    $gruppo = GruppoFamiliare::all()->random();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));

    $tot = PopolazioneNomadelfia::totalePopolazione();
    $pop = PopolazioneNomadelfia::popolazione();
    $this->assertEquals($tot, count($pop));

    $data_decesso = Carbon::now()->addYears(5)->toDatestring();
    $persona->deceduto($data_decesso);

    $persona = Persona::findOrFail($persona->id);
    expect($persona->isDeceduto())->toBeTrue()
        ->and($persona->isPersonaInterna())->toBeFalse()
        ->and(PopolazioneNomadelfia::totalePopolazione())->toBe($tot - 1);
    $this->assertNull($persona->posizioneAttuale());
    expect($persona->posizioniStorico()->get()->last()->pivot->data_fine)->toBe($data_decesso);
    $this->assertNull($persona->statoAttuale());
    expect($persona->statiStorico()->get()->last()->pivot->data_fine)->toBe($data_decesso);
    $this->assertNull($persona->gruppofamiliareAttuale());
    expect($persona->gruppofamiliariStorico()->get()->last()->pivot->data_uscita_gruppo)->toBe($data_decesso)
        ->and($persona->famigliaAttuale())->toBeNull()
        ->and($persona->famiglieStorico()->get()->last()->pivot->data_uscita)->toBe($data_decesso);

    $pop = PopolazioneNomadelfia::popolazione();
    expect(count($pop))->toBe($tot - 1);
});

it('manage exit of an adult', function () {
    $persona = Persona::factory()->maggiorenne()->maschio()->create();

    $data_entrata = Carbon::now()->toDatestring();
    $gruppo = GruppoFamiliare::all()->random();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));

    $azienda = Azienda::factory()->create();
    $persona->assegnaLavoratoreAzienda($azienda, $data_entrata);
    expect($persona->aziendeAttuali()->count())->toBe(1);
    // assegna incarico
    $incarico = Incarico::factory()->create();
    $persona->assegnaLavoratoreIncarico($incarico, Carbon::now());
    expect($incarico->lavoratoriAttuali()->count())->toBe(1);

    $tot = PopolazioneNomadelfia::totalePopolazione();
    $pop = PopolazioneNomadelfia::popolazione();
    expect(count($pop))->toBe($tot);

    $data_uscita = Carbon::now()->addYears(5)->toDatestring();
    $action = app(UscitaDaNomadelfiaAction::class);
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
        ->and($persona->famigliaAttuale())->not->toBeNull()
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
it('manage exit of underage', function () {
    $persona = Persona::factory()->minorenne()->maschio()->create();

    $gruppo = GruppoFamiliare::all()->random();

    $famiglia = Famiglia::factory()->create();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $capoFam->gruppifamiliari()->attach($gruppo->id,
        ['stato' => '1', 'data_entrata_gruppo' => Carbon::now()->subYears(10)->toDatestring()]);
    $famiglia->componenti()->attach($capoFam->id,
        ['stato' => '1', 'posizione_famiglia' => 'CAPO FAMIGLIA', 'data_entrata' => Carbon::now()->toDatestring()]);

    $act = app(EntrataDallaNascitaAction::class);
    $act->execute($persona, Famiglia::findOrFail($famiglia->id));

    // assegna minorenne in una classe
    $a = Anno::createAnno(2100);
    $classe = $a->aggiungiClasse(ClasseTipo::all()->random());
    $classe->aggiungiAlunno($persona, \Carbon\Carbon::now());
    expect($classe->alunni()->count())->toBe(1);

    $tot = PopolazioneNomadelfia::totalePopolazione();
    $pop = PopolazioneNomadelfia::popolazione();
    expect($tot)->toBe(count($pop));

    $data_uscita = Carbon::now()->addYears(5)->toDatestring();

    $act = app(UscitaDaNomadelfiaAction::class);
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
        ->and($persona->famiglieStorico()->get()->last()->pivot->data_uscita)->toBe($data_uscita)
        ->and($classe->alunni()->count())->toBe(0);

    $pop = PopolazioneNomadelfia::popolazione();
    expect(count($pop))->toBe($tot - 1);
});

/*
* Testa l'uscita di una famiglia.
*/
it('manage exit of family', function () {
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
    $famiglia->assegnaCapoFamiglia($capoFam, $now);
    $famiglia->assegnaMoglie($moglie, $now);
    $act = app(EntrataDallaNascitaAction::class);
    $act->execute($fnato, Famiglia::findOrFail($famiglia->id));
    $act = app(EntrataMinorenneAccoltoAction::class);
    $act->execute($faccolto, Carbon::now()->addYears(2)->toDatestring(), $famiglia);

    expect(PopolazioneNomadelfia::totalePopolazione())->toBe($init_tot + 4);
    $pop = PopolazioneNomadelfia::popolazione();
    expect(count($pop))->toBe($init_tot + 4);

    $data_uscita = Carbon::now()->toDatestring();
    $famiglia->uscita($data_uscita);

    expect(PopolazioneNomadelfia::totalePopolazione())->toBe($init_tot);
    $pop = PopolazioneNomadelfia::popolazione();
    expect(count($pop))->toBe($init_tot);
});

/*
* Testa l'uscita di una famiglia con alcuni componeneti fuori dal nucleo.
* Controlla che i componenti fuori dal nucleo rimagono come persone interne.
*/
it('manage people not part of family when it exits', function () {
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
    $famiglia->assegnaCapoFamiglia($capoFam, $now);
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
    $famiglia->uscita($data_uscita);
    // controlla che il figlio fuori dal nucleo non è uscito
    expect(PopolazioneNomadelfia::totalePopolazione())->toBe($init_tot + 1);
    $pop = PopolazioneNomadelfia::popolazione();
    expect(count($pop))->toBe($init_tot + 1);
});

/*
* Testa il conteggio dei figli minorenni nella popolazione
*/
it('count the underages of the population', function () {
    $now = Carbon::now()->toDatestring();
    $famiglia = Famiglia::factory()->create();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia->assegnaCapoFamiglia($capoFam, $now);
    $gruppo = GruppoFamiliare::all()->random();
    $capoFam->assegnaGruppoFamiliare($gruppo, $now);

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
it('assign postulante and effettivo status', function () {
    // entrata maggiorenne
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::first();
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));
    $now = Carbon::now()->subYears(4);
    $persona->assegnaPostulante($now);
    expect($persona->posizioneAttuale()->isPostulante())->toBeTrue();

    $persona->assegnaNomadelfoEffettivo($now->subYears(1));
    expect($persona->posizioneAttuale()->isEffettivo())->toBeTrue();
});

it('returns the figli between two ages', function () {
    // store the actual figli (maybe inserted by other tests)
    $actualFigli = PopolazioneNomadelfia::figliDaEta(0, 18, 'nominativo', null)->count();

    $famiglia = Famiglia::factory()->create();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia->assegnaCapoFamiglia($capoFam, Carbon::now());
    $capoFam->assegnaGruppoFamiliare(GruppoFamiliare::all()->random(), Carbon::now());

    $p1 = Persona::factory()->create(['data_nascita' => Carbon::now()->subYears(3)->startOfYear()]); // 2018-01-01 00:00:00
    $p0 = Persona::factory()->create(['data_nascita' => Carbon::now()->subYears(3)]);                // 2018-now()
    $p2 = Persona::factory()->create(['data_nascita' => Carbon::now()->subYears(3)->endOfYear()]);   // 2018-12-31 23:59:59

    $pafter = Persona::factory()->create(['data_nascita' => Carbon::now()->subYears(2)->startOfYear()]);   // 2019-01-01 00:00:00

    //        $p0->entrataNatoInNomadelfia($famiglia->id);
    //        $p1->entrataNatoInNomadelfia($famiglia->id);
    //        $p2->entrataNatoInNomadelfia($famiglia->id);
    //        $pafter->entrataNatoInNomadelfia($famiglia->id);

    $act = app(EntrataDallaNascitaAction::class);
    $act->execute($p0, Famiglia::findOrFail($famiglia->id));
    $act = app(EntrataDallaNascitaAction::class);
    $act->execute($p1, Famiglia::findOrFail($famiglia->id));
    $act = app(EntrataDallaNascitaAction::class);
    $act->execute($p2, Famiglia::findOrFail($famiglia->id));
    $act = app(EntrataDallaNascitaAction::class);
    $act->execute($pafter, Famiglia::findOrFail($famiglia->id));

    expect(count(PopolazioneNomadelfia::figliDaEta(3, 4, 'nominativo', null, true)))->toBe(3)
        ->and(count(PopolazioneNomadelfia::figliDaEta(3, 4, 'nominativo', null, false)))->toBe(2)
        ->and(count(PopolazioneNomadelfia::figliDaEta(2, 4, 'nominativo', null, false)))->toBe(4)
        ->and(count(PopolazioneNomadelfia::figliDaEta(2, 4, 'nominativo', null, true)))->toBe(4);

});

it('return the count of population', function () {
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

    $action = app(UscitaDaNomadelfiaAction::class);
    $action->execute($persona, Carbon::now()->toDatestring());
    $afterUscita = PopolazioneNomadelfia::presente()->count();
    expect($before - $afterUscita)->toBe(0);
});
