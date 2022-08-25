<?php

namespace Tests\Unit;

use Domain\Nomadelfia\Persona\Actions\EntrataDallaNascitaAction;
use Domain\Nomadelfia\Persona\Actions\EntrataInNomadelfiaAction;
use Domain\Nomadelfia\Persona\Actions\EntrataMinorenneAccoltoAction;
use Domain\Nomadelfia\Persona\Actions\EntrataMinorenneConFamigliaAction;
use Tests\MigrateFreshDB;
use Tests\TestCase;
use Carbon;

use Tests\CreatesApplication;

use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;;

class PersonaTest extends TestCase
{
    use CreatesApplication, MigrateFreshDB;

    /** @test */
    public function testAssegnaGruppoFamiliare()
    {
        $persona = Persona::factory()->maggiorenne()->maschio()->create();
        $gruppo = GruppoFamiliare::first();
        $data_entrata = Carbon::now()->toDatestring();
        $persona->assegnaGruppoFamiliare($gruppo, $data_entrata);

        $attuale = $persona->gruppofamiliareAttuale();
        $this->assertEquals($attuale->id, $gruppo->id);
        $this->assertEquals($attuale->pivot->data_entrata_gruppo, $data_entrata);

        // nuovo gruppo 
        $newGruppo = GruppoFamiliare::all()->random();
        $data_entrata = Carbon::now()->addYears(3)->toDatestring();
        $persona->assegnaGruppoFamiliare($newGruppo, $data_entrata);
        $attuale = $persona->gruppofamiliareAttuale();
        $this->assertEquals($attuale->id, $newGruppo->id);
        $this->assertEquals($attuale->pivot->data_entrata_gruppo, $data_entrata);
        $storico = $persona->gruppofamiliariStorico()->get()->last();
        $this->assertEquals($storico->id, $gruppo->id);
        $this->assertEquals($storico->pivot->data_uscita_gruppo, $data_entrata);

    }

    /** @test */
    public function testEntrataMaschioDallaNascita()
    {  /*
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
        $figlio = Posizione::perNome("figlio");
        $celibe = Stato::perNome("celibe");

        $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
        $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
        $famiglia->componenti()->attach($capoFam->id,
            ['stato' => '1', 'posizione_famiglia' => "CAPO FAMIGLIA", 'data_entrata' => Carbon::now()->toDatestring()]);

        $act = new EntrataDallaNascitaAction(new EntrataInNomadelfiaAction());
        $act->execute($persona, Famiglia::findOrFail($famiglia->id));

        $this->assertTrue($persona->isPersonaInterna());
        $this->assertEquals($persona->getDataEntrataNomadelfia(), $persona->data_nascita);
        $this->assertEquals($persona->posizioneAttuale()->id, $figlio->id);
        $this->assertEquals($persona->posizioneAttuale()->pivot->data_inizio, $persona->data_nascita);
        $this->assertEquals($persona->statoAttuale()->id, $celibe->id);
        $this->assertEquals($persona->statoAttuale()->stato, $celibe->stato);
        $this->assertEquals($persona->statoAttuale()->pivot->data_inizio, $persona->data_nascita);
        $this->assertEquals($persona->gruppofamiliareAttuale()->id, $gruppo->id);
        $this->assertEquals($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo, $persona->data_nascita);
        $this->assertNotNull($persona->famigliaAttuale());
        $this->assertEquals($persona->famigliaAttuale()->pivot->data_entrata, $persona->data_nascita);
    }

    /** @test */
    public function testEntrataFemminaDallaNascita()
    {
        $data_entrata = Carbon::now()->toDatestring();
        $persona = Persona::factory()->minorenne()->femmina()->create();
        $famiglia = Famiglia::factory()->create();


        $gruppo = GruppoFamiliare::first();
        $figlio = Posizione::perNome("figlio");
        $nubile = Stato::perNome("nubile");

        $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
        $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
        $famiglia->componenti()->attach($capoFam->id,
            ['stato' => '1', 'posizione_famiglia' => "CAPO FAMIGLIA", 'data_entrata' => Carbon::now()->toDatestring()]);

        $act = new EntrataDallaNascitaAction(new EntrataInNomadelfiaAction());
        $act->execute($persona, Famiglia::findOrFail($famiglia->id));

        $this->assertTrue($persona->isPersonaInterna());
        $this->assertEquals($persona->getDataEntrataNomadelfia(), $persona->data_nascita);
        $this->assertEquals($persona->posizioneAttuale()->id, $figlio->id);
        $this->assertEquals($persona->posizioneAttuale()->pivot->data_inizio, $persona->data_nascita);
        $this->assertEquals($persona->statoAttuale()->id, $nubile->id);
        $this->assertEquals($persona->statoAttuale()->stato, $nubile->stato);
        $this->assertEquals($persona->statoAttuale()->pivot->data_inizio, $persona->data_nascita);
        $this->assertEquals($persona->gruppofamiliareAttuale()->id, $gruppo->id);
        $this->assertEquals($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo, $persona->data_nascita);
        $this->assertNotNull($persona->famigliaAttuale());
        $this->assertEquals($persona->famigliaAttuale()->pivot->data_entrata, $persona->data_nascita);
    }

    /** @test */
    public function testEntrataMinorenneFemminaAccolto()
    {
        $data_entrata = Carbon::now()->toDatestring();
        $persona = Persona::factory()->minorenne()->femmina()->create();
        $famiglia = Famiglia::factory()->create();


        $gruppo = GruppoFamiliare::first();
        $figlio = Posizione::perNome("figlio");
        $nubile = Stato::perNome("nubile");

        $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
        $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
        $famiglia->componenti()->attach($capoFam->id,
            ['stato' => '1', 'posizione_famiglia' => "CAPO FAMIGLIA", 'data_entrata' => Carbon::now()->toDatestring()]);

//        $persona->entrataMinorenneAccolto($data_entrata, $famiglia->id);
        $act = new EntrataMinorenneAccoltoAction(new EntrataInNomadelfiaAction());
        $act->execute($persona, $data_entrata, $famiglia);
        /*
        Persona interna (DE)
        Figlio (DE)
        Nubile/celibe (DN)
        Gruppo (DE)
        Famiglia Figlio Accolto (DE)
        */

        $this->assertTrue($persona->isPersonaInterna());
        $this->assertEquals($persona->getDataEntrataNomadelfia(), $data_entrata);
        $this->assertEquals($persona->posizioneAttuale()->id, $figlio->id);
        $this->assertEquals($persona->posizioneAttuale()->pivot->data_inizio, $data_entrata);
        $this->assertEquals($persona->statoAttuale()->id, $nubile->id);
        $this->assertEquals($persona->statoAttuale()->stato, $nubile->stato);
        $this->assertEquals($persona->statoAttuale()->pivot->data_inizio, $persona->data_nascita);
        $this->assertEquals($persona->gruppofamiliareAttuale()->id, $gruppo->id);
        $this->assertEquals($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo, $data_entrata);
        $this->assertNotNull($persona->famigliaAttuale());
        $this->assertEquals($persona->famigliaAttuale()->pivot->data_entrata, $data_entrata);
    }

    /** @test */
    public function testEntrataMinorenneMaschioAccolto()
    {
        $data_entrata = Carbon::now()->toDatestring();
        $persona = Persona::factory()->minorenne()->maschio()->create();
        $famiglia = Famiglia::factory()->create();

        $gruppo = GruppoFamiliare::first();
        $figlio = Posizione::perNome("figlio");
        $nubile = Stato::perNome("celibe");

        $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
        $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
        $famiglia->componenti()->attach($capoFam->id,
            ['stato' => '1', 'posizione_famiglia' => "CAPO FAMIGLIA", 'data_entrata' => Carbon::now()->toDatestring()]);

        $act = new EntrataMinorenneAccoltoAction(new EntrataInNomadelfiaAction());
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
        $this->assertEquals($persona->getDataEntrataNomadelfia(), $data_entrata);
        $this->assertEquals($persona->posizioneAttuale()->id, $figlio->id);
        $this->assertEquals($persona->posizioneAttuale()->pivot->data_inizio, $data_entrata);
        $this->assertEquals($persona->statoAttuale()->id, $nubile->id);
        $this->assertEquals($persona->statoAttuale()->stato, $nubile->stato);
        $this->assertEquals($persona->statoAttuale()->pivot->data_inizio, $persona->data_nascita);
        $this->assertEquals($persona->gruppofamiliareAttuale()->id, $gruppo->id);
        $this->assertEquals($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo, $data_entrata);
        $this->assertNotNull($persona->famigliaAttuale());
        $this->assertEquals($persona->famigliaAttuale()->pivot->data_entrata, $data_entrata);
    }

    /** @test */
    public function testEntrataMinorenneFemminaConFamiglia()
    {
        $data_entrata = Carbon::now()->toDatestring();
        $persona = Persona::factory()->minorenne()->femmina()->create();
        $famiglia = Famiglia::factory()->create();

        $gruppo = GruppoFamiliare::first();
        $figlio = Posizione::perNome("figlio");
        $nubile = Stato::perNome("nubile");

        $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
        $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
        $famiglia->componenti()->attach($capoFam->id,
            ['stato' => '1', 'posizione_famiglia' => "CAPO FAMIGLIA", 'data_entrata' => Carbon::now()->toDatestring()]);

        $action = new EntrataMinorenneConFamigliaAction(new EntrataInNomadelfiaAction()     );
        $action->execute($persona, $data_entrata, Famiglia::findOrFail($famiglia->id));
        /*
            Persona interna (DE)
            Figlio (DE)
            Nubile/celibe (DN)
            Gruppo (DE)
            Famiglia Figlio (DN)
        */

        $this->assertTrue($persona->isPersonaInterna());
        $this->assertEquals($persona->getDataEntrataNomadelfia(), $data_entrata);
        $this->assertEquals($persona->posizioneAttuale()->id, $figlio->id);
        $this->assertEquals($persona->posizioneAttuale()->pivot->data_inizio, $data_entrata);
        $this->assertEquals($persona->statoAttuale()->id, $nubile->id);
        $this->assertEquals($persona->statoAttuale()->stato, $nubile->stato);
        $this->assertEquals($persona->statoAttuale()->pivot->data_inizio, $persona->data_nascita);
        $this->assertEquals($persona->gruppofamiliareAttuale()->id, $gruppo->id);
        $this->assertEquals($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo, $data_entrata);
        $this->assertNotNull($persona->famigliaAttuale());
        $this->assertEquals($persona->famigliaAttuale()->pivot->data_entrata, $persona->data_nascita);
    }

    /** @test */
    public function testEntrataMinorenneMaschioConFamiglia()
    {
        $data_entrata = Carbon::now()->toDatestring();
        $persona = Persona::factory()->minorenne()->maschio()->create();
        $famiglia = Famiglia::factory()->create();

        $gruppo = GruppoFamiliare::first();
        $figlio = Posizione::perNome("figlio");
        $nubile = Stato::perNome("celibe");

        $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
        $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
        $famiglia->componenti()->attach($capoFam->id,
            ['stato' => '1', 'posizione_famiglia' => "CAPO FAMIGLIA", 'data_entrata' => Carbon::now()->toDatestring()]);

        $action = new EntrataMinorenneConFamigliaAction(new EntrataInNomadelfiaAction()     );
        $action->execute($persona, $data_entrata, Famiglia::findOrFail($famiglia->id));
        /*
            Persona interna (DE)
            Figlio (DE)
            Nubile/celibe (DN)
            Gruppo (DE)
            Famiglia Figlio (DN)
        */

        $this->assertTrue($persona->isPersonaInterna());
        $this->assertEquals($persona->getDataEntrataNomadelfia(), $data_entrata);
        $this->assertEquals($persona->posizioneAttuale()->id, $figlio->id);
        $this->assertEquals($persona->posizioneAttuale()->pivot->data_inizio, $data_entrata);
        $this->assertEquals($persona->statoAttuale()->id, $nubile->id);
        $this->assertEquals($persona->statoAttuale()->stato, $nubile->stato);
        $this->assertEquals($persona->statoAttuale()->pivot->data_inizio, $persona->data_nascita);
        $this->assertEquals($persona->gruppofamiliareAttuale()->id, $gruppo->id);
        $this->assertEquals($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo, $data_entrata);
        $this->assertNotNull($persona->famigliaAttuale());
        $this->assertEquals($persona->famigliaAttuale()->pivot->data_entrata, $persona->data_nascita);
    }

    /** @test */
    public function testEntrataMaggiorenneMaschioSingle()
    {
        $data_entrata = Carbon::now()->toDatestring();
        $persona = Persona::factory()->maggiorenne()->maschio()->create();
        $gruppo = GruppoFamiliare::first();
        $ospite = Posizione::perNome("ospite");
        $celibe = Stato::perNome("celibe");

        $persona->entrataMaggiorenneSingle($data_entrata, $gruppo->id);

        $this->assertTrue($persona->isPersonaInterna());
        $this->assertEquals($persona->getDataEntrataNomadelfia(), $data_entrata);
        $this->assertEquals($persona->posizioneAttuale()->id, $ospite->id);
        $this->assertEquals($persona->posizioneAttuale()->pivot->data_inizio, $data_entrata);
        $this->assertEquals($persona->statoAttuale()->id, $celibe->id);
        $this->assertEquals($persona->statoAttuale()->pivot->data_inizio, $persona->data_nascita);
        $this->assertEquals($persona->gruppofamiliareAttuale()->id, $gruppo->id);
        $this->assertEquals($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo, $data_entrata);

        $this->assertNotNull($persona->famigliaAttuale());
        // check that the date creation of the family is when the person is 18 years old.
        $this->assertEquals($persona->famigliaAttuale()->data_creazione,
            Carbon::parse($persona->data_nascita)->addYears(18)->toDatestring());
    }

    /** @test */
    public function testEntrataMaggiorenneFemminaSingle()
    {
        $data_entrata = Carbon::now()->toDatestring();
        $persona = Persona::factory()->maggiorenne()->femmina()->create();
        $gruppo = GruppoFamiliare::first();
        $ospite = Posizione::perNome("ospite");
        $nubile = Stato::perNome("nubile");

        $persona->entrataMaggiorenneSingle($data_entrata, $gruppo->id);

        $this->assertTrue($persona->isPersonaInterna());
        $this->assertEquals($persona->getDataEntrataNomadelfia(), $data_entrata);
        $this->assertEquals($persona->posizioneAttuale()->id, $ospite->id);
        $this->assertEquals($persona->posizioneAttuale()->pivot->data_inizio, $data_entrata);
        $this->assertEquals($persona->statoAttuale()->id, $nubile->id);
        $this->assertEquals($persona->statoAttuale()->pivot->data_inizio, $persona->data_nascita);
        $this->assertEquals($persona->gruppofamiliareAttuale()->id, $gruppo->id);
        $this->assertEquals($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo, $data_entrata);

        $this->assertNotNull($persona->famigliaAttuale());
        // check that the date creation of the family is when the person is 18 years old.
        $this->assertEquals($persona->famigliaAttuale()->data_creazione,
            Carbon::parse($persona->data_nascita)->addYears(18)->toDatestring());
    }

    /** @test */
    public function testEntrataMaggiorenneSposato()
    {
        $data_entrata = Carbon::now()->toDatestring();
        $persona = Persona::factory()->maggiorenne()->create();
        $gruppo = GruppoFamiliare::first();
        $ospite = Posizione::perNome("ospite");

        $persona->entrataMaggiorenneSposato($data_entrata, $gruppo->id);

        $this->assertTrue($persona->isPersonaInterna());
        $this->assertEquals($persona->getDataEntrataNomadelfia(), $data_entrata);
        $this->assertEquals($persona->posizioneAttuale()->id, $ospite->id);
        $this->assertEquals($persona->posizioneAttuale()->pivot->data_inizio, $data_entrata);
        $this->assertEquals($persona->statoAttuale(), null);
        $this->assertEquals($persona->gruppofamiliareAttuale()->id, $gruppo->id);
        $this->assertEquals($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo, $data_entrata);

        $this->assertNull($persona->famigliaAttuale());
    }

    /** @test */
    public function testRientroMaggiorenneInNomadelfia()
    {
        $data_entrata = Carbon::now()->toDatestring();
        $persona = Persona::factory()->maggiorenne()->maschio()->create();
        $famiglia = Famiglia::factory()->create();
        $gruppo = GruppoFamiliare::first();
        $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
        $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
        $famiglia->componenti()->attach($capoFam->id,
            ['stato' => '1', 'posizione_famiglia' => "CAPO FAMIGLIA", 'data_entrata' => Carbon::now()->toDatestring()]);

        // la persona nasce in Nomadelfia
                $act = new EntrataDallaNascitaAction(new EntrataInNomadelfiaAction());
        $act->execute($persona, Famiglia::findOrFail($famiglia->id));
        $this->assertTrue($persona->isPersonaInterna());
        $data_entrata = Carbon::parse($persona->data_nascita);
        $this->assertEquals($persona->getDataEntrataNomadelfia(), $data_entrata->toDatestring());

        // la persona esce dalla comunità
        $data_uscita = Carbon::now()->addYears(5)->toDatestring();
        $persona->uscita($data_uscita);
        $this->assertFalse($persona->isPersonaInterna());
        $this->assertEquals($persona->getDataEntrataNomadelfia(), $data_entrata->toDatestring());
        $this->assertEquals($persona->getDataUscitaNomadelfia(), $data_uscita);

        // la persona rientra in Nomadelfia da maggiorenne adulto
        $data_rientro = Carbon::now()->addYears(10)->toDatestring();
        $persona->entrataMaggiorenneSingle($data_rientro, GruppoFamiliare::all()->random()->id);
        $this->assertTrue($persona->isPersonaInterna());
        $this->assertEquals($persona->getDataEntrataNomadelfia(), $data_rientro);
        $this->assertEquals($persona->getDataUscitaNomadelfia(), $data_uscita);
    }

    /** @test */
    public function testRientroMinorenneInNuovaFamigliaNomadelfia()
    {
        $data_entrata = Carbon::now();
        $persona = Persona::factory()->maggiorenne()->create();
        $gruppo = GruppoFamiliare::first();
        $persona->entrataMaggiorenneSposato($data_entrata, $gruppo->id);
        // viene creata la famiglia e aggiunto come campo famiglia
        $famiglia = Famiglia::factory()->create();
        $famiglia->assegnaCapoFamiglia($persona);
        $figlio = Persona::factory()->minorenne()->create();

        // il minorenne entra con la sua famiglia in Nomadelfia
        $action =new EntrataMinorenneConFamigliaAction(new EntrataInNomadelfiaAction()     );
        $action->execute($figlio, $data_entrata, Famiglia::findOrFail($famiglia->id));
        $this->assertTrue($figlio->isPersonaInterna());
        $this->assertEquals($figlio->getDataEntrataNomadelfia(), $data_entrata->toDatestring());

        // la famiglia esce da Nomadelfia
        $data_uscita = Carbon::now()->addYears(5)->toDatestring();
        $famiglia->uscita($data_uscita);
        $this->assertFalse($figlio->isPersonaInterna());
        $this->assertEquals($figlio->getDataEntrataNomadelfia(), $data_entrata->toDatestring());
        $this->assertEquals($figlio->getDataUscitaNomadelfia(), $data_uscita);

        // la persona rientra in Nomadelfia in una nuova famiglia
        $famiglia_rientro = Famiglia::factory()->create();
        $cp = Persona::factory()->maggiorenne()->create();
        $cp->assegnaGruppoFamiliare(GruppoFamiliare::first(), Carbon::now());
        $famiglia_rientro->assegnaCapoFamiglia($cp);
        $this->assertCount(0, $famiglia_rientro->figliAttuali()->get());

        $data_rientro = Carbon::now()->addYears(10)->toDatestring();
//        $figlio->entrataMinorenneAccolto($data_rientro, $famiglia_rientro->id);

        $act = new EntrataMinorenneAccoltoAction(new EntrataInNomadelfiaAction());
        $act->execute($figlio, $data_rientro, $famiglia_rientro);
        $this->assertTrue($figlio->isPersonaInterna());
        $this->assertEquals($figlio->getDataEntrataNomadelfia(), $data_rientro);
        $this->assertEquals($figlio->getDataUscitaNomadelfia(), $data_uscita);
        $this->assertCount(1, $famiglia_rientro->figliAttuali()->get());
    }

    /** @test */
    public function testRientroFamigliaInNomadelfia()
    {
        $data_entrata = Carbon::now()->toDatestring();
        $persona = Persona::factory()->maggiorenne()->create();
        $gruppo = GruppoFamiliare::first();
        $persona->entrataMaggiorenneSposato($data_entrata, $gruppo->id);

        // viene creata la famiglia e aggiunto come campo famiglia
        $famiglia = Famiglia::factory()->create();
        $famiglia->assegnaCapoFamiglia($persona);
        $figlio = Persona::factory()->minorenne()->create();

        $act = new EntrataDallaNascitaAction(new EntrataInNomadelfiaAction());
        $act->execute($figlio, Famiglia::findOrFail($famiglia->id));


        // la famiglia esce da Nomadelfia
        $data_uscita = Carbon::now()->addYear(10)->toDatestring();
        $famiglia->uscita($data_uscita);

        $famiglia->componentiAttuali()->get()->each(function ($componente) use ($data_entrata, $data_uscita) {
            $this->assertFalse($componente->isPersonaInterna());
            if ($componente->isCapoFamiglia()) {
                $this->assertEquals($componente->getDataEntrataNomadelfia(), $data_entrata);
            } else {
                $this->assertEquals($componente->getDataEntrataNomadelfia(), $componente->data_nascita);
            }
            $this->assertEquals($componente->getDataUscitaNomadelfia(), $data_uscita);
        });

        // la famiglia rientra a Nomadelfia. Prima entra il capofamiglia
        $data_rientro = Carbon::now()->addYear(20)->toDatestring();
        $persona->entrataMaggiorenneSposato($data_rientro, GruppoFamiliare::all()->random()->id);
        $action = new EntrataMinorenneConFamigliaAction(new EntrataInNomadelfiaAction()     );
        $action->execute($figlio, $data_rientro, Famiglia::findOrFail($famiglia->id));
        $famiglia->componentiAttuali()->get()->each(function ($componente) use ($data_rientro, $data_uscita) {
            $this->assertTrue($componente->isPersonaInterna());
            $this->assertEquals($componente->getDataEntrataNomadelfia(), $data_rientro);
            $this->assertEquals($componente->getDataUscitaNomadelfia(), $data_uscita);
        });
    }

    /** @test */
    public function get_persone_from_eta()
    {
        $init  = Persona::all()->count();
        $persona2 = Persona::factory()->nato(Carbon::parse('01-01-1991'))->maschio()->create();
        $persona1 = Persona::factory()->nato(Carbon::parse("18-04-1991"))->maschio()->create();
        $persona2 = Persona::factory()->nato(Carbon::parse('31-12-1991'))->maschio()->create();
        $after  = Persona::all()->count();
        $this->assertEquals(3, $after  - $init );
        $this->assertEquals(3, Persona::NatiInAnno(1991)->count());

    }


    /** @test */
    public function build_numero_elenco_per_persona()
    {
        Persona::factory()->create(['numero_elenco' => 'A1']);
        Persona::factory()->create(['numero_elenco' => 'A9']);
        $pLast = Persona::factory()->create(['cognome' => 'Aminoacido']);

        $res = Persona::NumeroElencoPrefixByLetter('A')->first();
        $this->assertEquals(9,  $res->numero);
        $this->assertEquals('A',  $res->lettera);

        $n = $pLast->proposeNumeroElenco();
        $this->assertEquals('A10',  $n);

    }
}
