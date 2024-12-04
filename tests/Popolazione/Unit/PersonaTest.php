<?php

declare(strict_types=1);

namespace Tests\Unit;

use Carbon\Carbon;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Actions\ProposeNumeroElencoAction;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\AssegnaGruppoFamiliareAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataDallaNascitaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneAccoltoAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneConFamigliaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\UscitaFamigliaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\UscitaPersonaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;
use Illuminate\Support\Carbon as SupportCarbon;

it('assigns gruppo to a person', function (): void {
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::first();
    $data_entrata = Carbon::now();
    $action = app(AssegnaGruppoFamiliareAction::class);
    $action->execute($persona, $gruppo, $data_entrata);

    $attuale = $persona->gruppofamiliareAttuale();
    expect($attuale->id)->toBe($gruppo->id)
        ->and($attuale->pivot->data_entrata_gruppo)->toBe($data_entrata->toDateString());

    // nuovo gruppo
    $newGruppo = GruppoFamiliare::all()->random();
    $data_entrata = Carbon::now()->addYears(3); //->toDatestring();
    $action = app(AssegnaGruppoFamiliareAction::class);
    $action->execute($persona, $newGruppo, $data_entrata);

    $attuale = $persona->gruppofamiliareAttuale();
    expect($attuale->id)->toBe($newGruppo->id)
        ->and($attuale->pivot->data_entrata_gruppo)->toBe($data_entrata->toDateString());
    $storico = $persona->gruppofamiliariStorico()->get()->last();
    expect($storico->id)->toBe($gruppo->id)
        ->and($storico->pivot->data_uscita_gruppo)->toBe($data_entrata->toDateString());

});

it('testEntrataMaschioDallaNascita', function (): void {  /*
        Person interna (DN)
        Figlio (DN)
        Nubile(celibe (DN)
        Gruppo (DN)
        Famiglia, Figlio (DN)
        */
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $famiglia = Famiglia::factory()->create();

    $gruppo = GruppoFamiliare::first();
    $figlio = Posizione::perNome('figlio');
    $celibe = Stato::perNome('celibe');

    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
    $famiglia->componenti()->attach($capoFam->id,
        ['stato' => '1', 'posizione_famiglia' => 'CAPO FAMIGLIA']);

    $act = app(EntrataDallaNascitaAction::class);
    $act->execute($persona, Famiglia::findOrFail($famiglia->id));

    $this->assertTrue($persona->isPersonaInterna());
    expect($persona->getDataEntrataNomadelfia())->toBe($persona->data_nascita)
        ->and($persona->posizioneAttuale()->id)->toBe($figlio->id)
        ->and($persona->posizioneAttuale()->pivot->data_inizio)->toBe($persona->data_nascita)
        ->and($persona->statoAttuale()->id)->toBe($celibe->id)
        ->and($persona->statoAttuale()->stato)->toBe($celibe->stato)
        ->and($persona->statoAttuale()->pivot->data_inizio)->toBe($persona->data_nascita)
        ->and($persona->gruppofamiliareAttuale()->id)->toBe($gruppo->id)
        ->and($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo)->toBe($persona->data_nascita)
        ->and($persona->famigliaAttuale())->not->toBeNull()
        ->and($persona->famigliaAttuale()->id)->toBe($famiglia->id);
});

it('testEntrataFemminaDallaNascita', function (): void {
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->minorenne()->femmina()->create();
    $famiglia = Famiglia::factory()->create();

    $gruppo = GruppoFamiliare::first();
    $figlio = Posizione::perNome('figlio');
    $nubile = Stato::perNome('nubile');

    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
    $famiglia->componenti()->attach($capoFam->id,
        ['stato' => '1', 'posizione_famiglia' => 'CAPO FAMIGLIA']);

    $act = app(EntrataDallaNascitaAction::class);
    $act->execute($persona, Famiglia::findOrFail($famiglia->id));

    $this->assertTrue($persona->isPersonaInterna());
    expect($persona->getDataEntrataNomadelfia())->toBe($persona->data_nascita)
        ->and($persona->posizioneAttuale()->id)->toBe($figlio->id)
        ->and($persona->posizioneAttuale()->pivot->data_inizio)->toBe($persona->data_nascita)
        ->and($persona->statoAttuale()->id)->toBe($nubile->id)
        ->and($persona->statoAttuale()->stato)->toBe($nubile->stato)
        ->and($persona->statoAttuale()->pivot->data_inizio)->toBe($persona->data_nascita)
        ->and($persona->gruppofamiliareAttuale()->id)->toBe($gruppo->id)
        ->and($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo)->toBe($persona->data_nascita);
    $this->assertNotNull($persona->famigliaAttuale());
    expect($persona->famigliaAttuale()->id)->toBe($famiglia->id);
});

it('testEntrataMinorenneFemminaAccolto', function (): void {
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->minorenne()->femmina()->create();
    $famiglia = Famiglia::factory()->create();

    $gruppo = GruppoFamiliare::first();
    $figlio = Posizione::perNome('figlio');
    $nubile = Stato::perNome('nubile');

    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
    $famiglia->componenti()->attach($capoFam->id,
        ['stato' => '1', 'posizione_famiglia' => 'CAPO FAMIGLIA']);

    //        $persona->entrataMinorenneAccolto($data_entrata, $famiglia->id);
    $act = app(EntrataMinorenneAccoltoAction::class);
    $act->execute($persona, $data_entrata, $famiglia);
    /*
    Persona interna (DE)
    Figlio (DE)
    Nubile/celibe (DN)
    Gruppo (DE)
    Famiglia Figlio Accolto (DE)
    */

    $this->assertTrue($persona->isPersonaInterna());
    expect($persona->getDataEntrataNomadelfia())->toBe($data_entrata)
        ->and($persona->posizioneAttuale()->id)->toBe($figlio->id)
        ->and($persona->posizioneAttuale()->pivot->data_inizio)->toBe($data_entrata)
        ->and($persona->statoAttuale()->id)->toBe($nubile->id)
        ->and($persona->statoAttuale()->stato)->toBe($nubile->stato)
        ->and($persona->statoAttuale()->pivot->data_inizio)->toBe($persona->data_nascita)
        ->and($persona->gruppofamiliareAttuale()->id)->toBe($gruppo->id)
        ->and($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo)->toBe($data_entrata);
    $this->assertNotNull($persona->famigliaAttuale());
    expect($persona->famigliaAttuale()->id)->toBe($famiglia->id);
});

it('testEntrataMinorenneMaschioAccolto', function (): void {
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $famiglia = Famiglia::factory()->create();

    $gruppo = GruppoFamiliare::first();
    $figlio = Posizione::perNome('figlio');
    $nubile = Stato::perNome('celibe');

    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
    $famiglia->componenti()->attach($capoFam->id,
        ['stato' => '1', 'posizione_famiglia' => 'CAPO FAMIGLIA']);

    $act = app(EntrataMinorenneAccoltoAction::class);
    $act->execute($persona, $data_entrata, $famiglia);
    //        $persona->entrataMinorenneAccolto($data_entrata, $famiglia->id);
    /*
    Persona interna (DE)
    Figlio (DE)
    Nubile/celibe (DN)
    Gruppo (DE)
    Famiglia Figlio Accolto (DE)
    */

    $this->assertTrue($persona->isPersonaInterna());
    expect($persona->getDataEntrataNomadelfia())->toBe($data_entrata)
        ->and($persona->posizioneAttuale()->id)->toBe($figlio->id)
        ->and($persona->posizioneAttuale()->pivot->data_inizio)->toBe($data_entrata)
        ->and($persona->statoAttuale()->id)->toBe($nubile->id)
        ->and($persona->statoAttuale()->stato)->toBe($nubile->stato)
        ->and($persona->statoAttuale()->pivot->data_inizio)->toBe($persona->data_nascita)
        ->and($persona->gruppofamiliareAttuale()->id)->toBe($gruppo->id)
        ->and($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo)->toBe($data_entrata);
    $this->assertNotNull($persona->famigliaAttuale());
    expect($persona->famigliaAttuale()->id)->toBe($famiglia->id);
});

it('testEntrataMinorenneFemminaConFamiglia', function (): void {
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->minorenne()->femmina()->create();
    $famiglia = Famiglia::factory()->create();

    $gruppo = GruppoFamiliare::first();
    $figlio = Posizione::perNome('figlio');
    $nubile = Stato::perNome('nubile');

    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
    $famiglia->componenti()->attach($capoFam->id,
        ['stato' => '1', 'posizione_famiglia' => 'CAPO FAMIGLIA']);

    $action = app(EntrataMinorenneConFamigliaAction::class);
    $action->execute($persona, $data_entrata, Famiglia::findOrFail($famiglia->id));
    /*
        Persona interna (DE)
        Figlio (DE)
        Nubile/celibe (DN)
        Gruppo (DE)
        Famiglia Figlio (DN)
    */

    $this->assertTrue($persona->isPersonaInterna());
    expect($persona->getDataEntrataNomadelfia())->toBe($data_entrata)
        ->and($persona->posizioneAttuale()->id)->toBe($figlio->id)
        ->and($persona->posizioneAttuale()->pivot->data_inizio)->toBe($data_entrata)
        ->and($persona->statoAttuale()->id)->toBe($nubile->id)
        ->and($persona->statoAttuale()->stato)->toBe($nubile->stato)
        ->and($persona->statoAttuale()->pivot->data_inizio)->toBe($persona->data_nascita)
        ->and($persona->gruppofamiliareAttuale()->id)->toBe($gruppo->id)
        ->and($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo)->toBe($data_entrata);
    $this->assertNotNull($persona->famigliaAttuale());
    expect($persona->famigliaAttuale()->id)->toBe($famiglia->id);
});

it('testEntrataMinorenneMaschioConFamiglia', function (): void {
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->minorenne()->maschio()->create();
    $famiglia = Famiglia::factory()->create();

    $gruppo = GruppoFamiliare::first();
    $figlio = Posizione::perNome('figlio');
    $nubile = Stato::perNome('celibe');

    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
    $famiglia->componenti()->attach($capoFam->id,
        ['stato' => '1', 'posizione_famiglia' => 'CAPO FAMIGLIA']);

    $action = app(EntrataMinorenneConFamigliaAction::class);
    $action->execute($persona, $data_entrata, Famiglia::findOrFail($famiglia->id));
    /*
        Persona interna (DE)
        Figlio (DE)
        Nubile/celibe (DN)
        Gruppo (DE)
        Famiglia Figlio (DN)
    */

    $this->assertTrue($persona->isPersonaInterna());
    expect($persona->getDataEntrataNomadelfia())->toBe($data_entrata)
        ->and($persona->posizioneAttuale()->id)->toBe($figlio->id)
        ->and($persona->posizioneAttuale()->pivot->data_inizio)->toBe($data_entrata)
        ->and($persona->statoAttuale()->id)->toBe($nubile->id)
        ->and($persona->statoAttuale()->stato)->toBe($nubile->stato)
        ->and($persona->statoAttuale()->pivot->data_inizio)->toBe($persona->data_nascita)
        ->and($persona->gruppofamiliareAttuale()->id)->toBe($gruppo->id)
        ->and($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo)->toBe($data_entrata);
    $this->assertNotNull($persona->famigliaAttuale());
    expect($persona->famigliaAttuale()->id)->toBe($famiglia->id);
});

it('testEntrataMaggiorenneMaschioSingle', function (): void {
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $gruppo = GruppoFamiliare::first();
    $ospite = Posizione::perNome('ospite');
    $celibe = Stato::perNome('celibe');

    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));

    $this->assertTrue($persona->isPersonaInterna());
    expect($persona->getDataEntrataNomadelfia(), $data_entrata);
    expect($persona->posizioneAttuale()->id, $ospite->id);
    expect($persona->posizioneAttuale()->pivot->data_inizio, $data_entrata);
    expect($persona->statoAttuale()->id, $celibe->id);
    expect($persona->statoAttuale()->pivot->data_inizio, $persona->data_nascita);
    expect($persona->gruppofamiliareAttuale()->id, $gruppo->id);
    expect($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo, $data_entrata);

    expect($persona->famigliaAttuale())->toBeNull();
});

it('testEntrataMaggiorenneFemminaSingle', function (): void {
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->maggiorenne()->femmina()->create();
    $gruppo = GruppoFamiliare::first();
    $ospite = Posizione::perNome('ospite');
    $nubile = Stato::perNome('nubile');

    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_entrata, GruppoFamiliare::findOrFail($gruppo->id));

    $this->assertTrue($persona->isPersonaInterna());
    expect($persona->getDataEntrataNomadelfia())->toBe($data_entrata)
        ->and($persona->posizioneAttuale()->id)->toBe($ospite->id)
        ->and($persona->posizioneAttuale()->pivot->data_inizio)->toBe($data_entrata)
        ->and($persona->statoAttuale()->id)->toBe($nubile->id)
        ->and($persona->statoAttuale()->pivot->data_inizio)->toBe($persona->data_nascita)
        ->and($persona->gruppofamiliareAttuale()->id)->toBe($gruppo->id)
        ->and($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo)->toBe($data_entrata);

    expect($persona->famigliaAttuale())->toBeNull();
});

it('testEntrataMaggiorenneSposato', function (): void {
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->maggiorenne()->create();
    $gruppo = GruppoFamiliare::first();
    $ospite = Posizione::perNome('ospite');

    $act = app(EntrataMaggiorenneConFamigliaAction::class);
    $act->execute($persona, $data_entrata, $gruppo);

    $this->assertTrue($persona->isPersonaInterna());
    expect($persona->getDataEntrataNomadelfia())->toBe($data_entrata)
        ->and($persona->posizioneAttuale()->id)->toBe($ospite->id)
        ->and($persona->posizioneAttuale()->pivot->data_inizio)->toBe($data_entrata)
        ->and($persona->statoAttuale())->toBe(null)
        ->and($persona->gruppofamiliareAttuale()->id)->toBe($gruppo->id)
        ->and($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo)->toBe($data_entrata)
        ->and($persona->famigliaAttuale())->toBeNull();
});

it('testRientroMaggiorenneInNomadelfia', function (): void {
    $data_entrata = SupportCarbon::now()->toDatestring();
    $persona = Persona::factory()->maggiorenne()->maschio()->create();
    $famiglia = Famiglia::factory()->create();
    $gruppo = GruppoFamiliare::first();
    $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
    $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
    $famiglia->componenti()->attach($capoFam->id,
        ['stato' => '1', 'posizione_famiglia' => 'CAPO FAMIGLIA']);

    // la persona nasce in Nomadelfia
    $act = app(EntrataDallaNascitaAction::class);
    $act->execute($persona, Famiglia::findOrFail($famiglia->id));
    $this->assertTrue($persona->isPersonaInterna());
    $data_entrata = Carbon::parse($persona->data_nascita);
    expect($persona->getDataEntrataNomadelfia())->toBe($data_entrata->toDatestring());

    // la persona esce dalla comunità
    $data_uscita = Carbon::now()->addYears(5)->toDatestring();

    $act = app(UscitaPersonaAction::class);
    $act->execute($persona, $data_uscita, true);

    $this->assertFalse($persona->isPersonaInterna());
    expect($persona->getDataEntrataNomadelfia())->toBe($data_entrata->toDatestring())
        ->and($persona->getDataUscitaNomadelfia())->toBe($data_uscita);

    // la persona rientra in Nomadelfia da maggiorenne adulto
    $data_rientro = Carbon::now()->addYears(10)->toDatestring();
    //        $persona->entrataMaggiorenneSingle($data_rientro, ->id);
    $action = app(EntrataMaggiorenneSingleAction::class);
    $action->execute($persona, $data_rientro, GruppoFamiliare::all()->random());
    $this->assertTrue($persona->isPersonaInterna());
    expect($persona->getDataEntrataNomadelfia())->toBe($data_rientro)
        ->and($persona->getDataUscitaNomadelfia())->toBe($data_uscita);
});

it('testRientroMinorenneInNuovaFamigliaNomadelfia', function (): void {
    $data_entrata = Carbon::now();
    $persona = Persona::factory()->maggiorenne()->create();
    $gruppo = GruppoFamiliare::first();
    $act = app(EntrataMaggiorenneConFamigliaAction::class);
    $act->execute($persona, $data_entrata, $gruppo);
    // viene creata la famiglia e aggiunto come campo famiglia
    $famiglia = Famiglia::factory()->create();
    $famiglia->assegnaCapoFamiglia($persona);
    $figlio = Persona::factory()->minorenne()->create();

    // il minorenne entra con la sua famiglia in Nomadelfia
    $action = app(EntrataMinorenneConFamigliaAction::class);
    $action->execute($figlio, $data_entrata, Famiglia::findOrFail($famiglia->id));
    $this->assertTrue($figlio->isPersonaInterna());
    expect($figlio->getDataEntrataNomadelfia())->toBe($data_entrata->toDatestring());

    // la famiglia esce da Nomadelfia
    $data_uscita = Carbon::now()->addYears(5)->toDatestring();
    $action = app(UscitaFamigliaAction::class);
    $action->execute($famiglia, $data_uscita);

    $this->assertFalse($figlio->isPersonaInterna());
    expect($figlio->getDataEntrataNomadelfia())->toBe($data_entrata->toDatestring());
    expect($figlio->getDataUscitaNomadelfia())->toBe($data_uscita);

    // la persona rientra in Nomadelfia in una nuova famiglia
    $famiglia_rientro = Famiglia::factory()->create();
    $cp = Persona::factory()->maggiorenne()->create();
    $action = app(AssegnaGruppoFamiliareAction::class);
    $action->execute($cp, GruppoFamiliare::first(), Carbon::now());
    $famiglia_rientro->assegnaCapoFamiglia($cp);
    $this->assertCount(0, $famiglia_rientro->figliAttuali()->get());

    $data_rientro = Carbon::now()->addYears(10)->toDatestring();
    //        $figlio->entrataMinorenneAccolto($data_rientro, $famiglia_rientro->id);

    $act = app(EntrataMinorenneAccoltoAction::class);
    $act->execute($figlio, $data_rientro, $famiglia_rientro);
    $this->assertTrue($figlio->isPersonaInterna());
    expect($figlio->getDataEntrataNomadelfia())->toBe($data_rientro)
        ->and($figlio->getDataUscitaNomadelfia())->toBe($data_uscita)
        ->and($famiglia_rientro->figliAttuali()->count())->toBe(1);
});

it('testRientroFamigliaInNomadelfia', function (): void {
    $data_entrata = Carbon::now()->toDatestring();
    $persona = Persona::factory()->maggiorenne()->create();
    $gruppo = GruppoFamiliare::first();
    $act = app(EntrataMaggiorenneConFamigliaAction::class);
    $act->execute($persona, $data_entrata, $gruppo);

    // viene creata la famiglia e aggiunto come campo famiglia
    $famiglia = Famiglia::factory()->create();
    $famiglia->assegnaCapoFamiglia($persona);
    $figlio = Persona::factory()->minorenne()->create();

    $act = app(EntrataDallaNascitaAction::class);
    $act->execute($figlio, Famiglia::findOrFail($famiglia->id));

    // la famiglia esce da Nomadelfia
    $data_uscita = Carbon::now()->addYear(10)->toDatestring();
    $action = app(UscitaFamigliaAction::class);
    $action->execute($famiglia, $data_uscita);

    $famiglia->componentiAttuali()->get()->each(function ($componente) use ($data_entrata, $data_uscita): void {
        $this->assertFalse($componente->isPersonaInterna());
        if ($componente->isCapoFamiglia()) {
            expect($componente->getDataEntrataNomadelfia())->toBe($data_entrata);
        } else {
            expect($componente->getDataEntrataNomadelfia())->toBe($componente->data_nascita);
        }
        expect($componente->getDataUscitaNomadelfia())->toBe($data_uscita);
    });

    // la famiglia rientra a Nomadelfia. Prima entra il capofamiglia
    $data_rientro = Carbon::now()->addYear(20)->toDatestring();
    $act = app(EntrataMaggiorenneConFamigliaAction::class);
    $act->execute($persona, $data_rientro, GruppoFamiliare::all()->random());
    $action = app(EntrataMinorenneConFamigliaAction::class);
    $action->execute($figlio, $data_rientro, Famiglia::findOrFail($famiglia->id));
    $famiglia->componentiAttuali()->get()->each(function ($componente) use ($data_rientro, $data_uscita): void {
        $this->assertTrue($componente->isPersonaInterna());
        expect($componente->getDataEntrataNomadelfia())->toBe($data_rientro);
        expect($componente->getDataUscitaNomadelfia())->toBe($data_uscita);
    });
});

it('get people nati in anno correctly', function (): void {
    Persona::factory()->nato(Carbon::parse('01-01-1791'))->maschio()->create();
    Persona::factory()->nato(Carbon::parse('18-04-1791'))->maschio()->create();
    Persona::factory()->nato(Carbon::parse('31-12-1791'))->maschio()->create();
    expect(Persona::natiInAnno(1791)->count())->toBe(3);
});

it('builds numero elenco', function (): void {
    Persona::factory()->create(['numero_elenco' => 'A1']);
    Persona::factory()->create(['numero_elenco' => 'A9']);
    $pLast = Persona::factory()->create(['cognome' => 'Aminoacido']);

    $last = Persona::NumeroElencoPrefixByLetter('A')->limit(1)->get()->first();
    expect($last->numero_elenco)->toBe('A9');

    $action = app(ProposeNumeroElencoAction::class);

    $n = $action->execute($pLast);
    expect($n)->toBe('A10');
});
