<?php

namespace Tests\Unit;

use Tests\MigrateFreshDB;
use Tests\TestCase;
use Carbon;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesApplication;

use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Models\Famiglia;
use App\Nomadelfia\Models\GruppoFamiliare;
use App\Nomadelfia\Models\Posizione;
use App\Nomadelfia\Models\Stato;

class PersonaTest extends TestCase
{
    use CreatesApplication, MigrateFreshDB;

    public function testAssegnaGruppoFamiliare()
    {
        $persona = factory(Persona::class)->states("maggiorenne", "maschio")->create();
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


    public function testEntrataMaschioDallaNascita()
    {  /*
        Person interna (DN)
        Figlio (DN)
        Nubile(celibe (DN)
        Gruppo (DN)
        Famiglia, Figlio (DN)
        */
        $data_entrata = Carbon::now()->toDatestring();
        $persona = factory(Persona::class)->states("minorenne", "maschio")->create();
        $famiglia = factory(Famiglia::class)->create();

        $gruppo = GruppoFamiliare::first();
        $figlio = Posizione::perNome("figlio");
        $celibe = Stato::perNome("celibe");

        $capoFam = factory(Persona::class)->states("maggiorenne", "maschio")->create();
        $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
        $famiglia->componenti()->attach($capoFam->id,
            ['stato' => '1', 'posizione_famiglia' => "CAPO FAMIGLIA", 'data_entrata' => Carbon::now()->toDatestring()]);

        $persona->entrataNatoInNomadelfia($famiglia->id);

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

    public function testEntrataFemminaDallaNascita()
    {
        $data_entrata = Carbon::now()->toDatestring();
        $persona = factory(Persona::class)->states("minorenne", "femmina")->create();
        $famiglia = factory(Famiglia::class)->create();


        $gruppo = GruppoFamiliare::first();
        $figlio = Posizione::perNome("figlio");
        $nubile = Stato::perNome("nubile");

        $capoFam = factory(Persona::class)->states("maggiorenne", "maschio")->create();
        $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
        $famiglia->componenti()->attach($capoFam->id,
            ['stato' => '1', 'posizione_famiglia' => "CAPO FAMIGLIA", 'data_entrata' => Carbon::now()->toDatestring()]);

        $persona->entrataNatoInNomadelfia($famiglia->id);

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

    public function testEntrataMinorenneFemminaAccolto()
    {
        $data_entrata = Carbon::now()->toDatestring();
        $persona = factory(Persona::class)->states("minorenne", "femmina")->create();
        $famiglia = factory(Famiglia::class)->create();


        $gruppo = GruppoFamiliare::first();
        $figlio = Posizione::perNome("figlio");
        $nubile = Stato::perNome("nubile");

        $capoFam = factory(Persona::class)->states("maggiorenne", "maschio")->create();
        $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
        $famiglia->componenti()->attach($capoFam->id,
            ['stato' => '1', 'posizione_famiglia' => "CAPO FAMIGLIA", 'data_entrata' => Carbon::now()->toDatestring()]);

        $persona->entrataMinorenneAccolto($data_entrata, $famiglia->id);
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

    public function testEntrataMinorenneMaschioAccolto()
    {
        $data_entrata = Carbon::now()->toDatestring();
        $persona = factory(Persona::class)->states("minorenne", "maschio")->create();
        $famiglia = factory(Famiglia::class)->create();

        $gruppo = GruppoFamiliare::first();
        $figlio = Posizione::perNome("figlio");
        $nubile = Stato::perNome("celibe");

        $capoFam = factory(Persona::class)->states("maggiorenne", "maschio")->create();
        $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
        $famiglia->componenti()->attach($capoFam->id,
            ['stato' => '1', 'posizione_famiglia' => "CAPO FAMIGLIA", 'data_entrata' => Carbon::now()->toDatestring()]);

        $persona->entrataMinorenneAccolto($data_entrata, $famiglia->id);
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

    public function testEntrataMinorenneFemminaConFamiglia()
    {
        $data_entrata = Carbon::now()->toDatestring();
        $persona = factory(Persona::class)->states("minorenne", "femmina")->create();
        $famiglia = factory(Famiglia::class)->create();

        $gruppo = GruppoFamiliare::first();
        $figlio = Posizione::perNome("figlio");
        $nubile = Stato::perNome("nubile");

        $capoFam = factory(Persona::class)->states("maggiorenne", "maschio")->create();
        $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
        $famiglia->componenti()->attach($capoFam->id,
            ['stato' => '1', 'posizione_famiglia' => "CAPO FAMIGLIA", 'data_entrata' => Carbon::now()->toDatestring()]);

        $persona->entrataMinorenneConFamiglia($data_entrata, $famiglia->id);
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

    public function testEntrataMinorenneMaschioConFamiglia()
    {
        $data_entrata = Carbon::now()->toDatestring();
        $persona = factory(Persona::class)->states("minorenne", "maschio")->create();
        $famiglia = factory(Famiglia::class)->create();

        $gruppo = GruppoFamiliare::first();
        $figlio = Posizione::perNome("figlio");
        $nubile = Stato::perNome("celibe");

        $capoFam = factory(Persona::class)->states("maggiorenne", "maschio")->create();
        $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
        $famiglia->componenti()->attach($capoFam->id,
            ['stato' => '1', 'posizione_famiglia' => "CAPO FAMIGLIA", 'data_entrata' => Carbon::now()->toDatestring()]);

        $persona->entrataMinorenneConFamiglia($data_entrata, $famiglia->id);
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

    public function testEntrataMaggiorenneMaschioSingle()
    {
        $data_entrata = Carbon::now()->toDatestring();
        $persona = factory(Persona::class)->states("maggiorenne", "maschio")->create();
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

    public function testEntrataMaggiorenneFemminaSingle()
    {
        $data_entrata = Carbon::now()->toDatestring();
        $persona = factory(Persona::class)->states("maggiorenne", "femmina")->create();
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


    public function testEntrataMaggiorenneSposato()
    {
        $data_entrata = Carbon::now()->toDatestring();
        $persona = factory(Persona::class)->states("maggiorenne")->create();
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

    public function testRientroMaggiorenneInNomadelfia()
    {
        $data_entrata = Carbon::now()->toDatestring();
        $persona = factory(Persona::class)->states("maggiorenne", "maschio")->create();
        $famiglia = factory(Famiglia::class)->create();
        $gruppo = GruppoFamiliare::first();
        $capoFam = factory(Persona::class)->states("maggiorenne", "maschio")->create();
        $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $data_entrata]);
        $famiglia->componenti()->attach($capoFam->id,
            ['stato' => '1', 'posizione_famiglia' => "CAPO FAMIGLIA", 'data_entrata' => Carbon::now()->toDatestring()]);

        // la persona nasce in Nomadelfia
        $persona->entrataNatoInNomadelfia($famiglia->id);
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


    public function testRientroFamigliaInNomadelfia()
    {
        $data_entrata = Carbon::now()->toDatestring();
        $persona = factory(Persona::class)->states("maggiorenne")->create();
        $gruppo = GruppoFamiliare::first();
        $persona->entrataMaggiorenneSposato($data_entrata, $gruppo->id);

        // viene creata la famiglia e aggiunto come campo famiglia
        $famiglia = factory(Famiglia::class)->create();
        $famiglia->assegnaCapoFamiglia($persona);
        $figlio = factory(Persona::class)->states("minorenne")->create();
        $figlio->entrataNatoInNomadelfia($famiglia->id);

        // la famiglia esce da Nomadelfia
        $data_uscita = Carbon::now()->addYear(10)->toDatestring();
        $famiglia->uscita($data_uscita);

        $famiglia->componentiAttuali()->get()->each(function ($componente) use ($data_entrata, $data_uscita) {
            $this->assertFalse($componente->isPersonaInterna());
            if ($componente->isCapoFamiglia()){
                $this->assertEquals($componente->getDataEntrataNomadelfia(), $data_entrata);
            }else{
                $this->assertEquals($componente->getDataEntrataNomadelfia(), $componente->data_nascita);
            }
            $this->assertEquals($componente->getDataUscitaNomadelfia(), $data_uscita);
        });

         // la famiglia rientra a Nomadelfia. Prima entra il capofamiglia
        $data_rientro = Carbon::now()->addYear(20)->toDatestring();
        $persona->entrataMaggiorenneSposato($data_rientro, GruppoFamiliare::all()->random()->id);
        $figlio->entrataMinorenneConFamiglia($data_rientro, $famiglia->id);
        $famiglia->componentiAttuali()->get()->each(function ($componente) use ($data_rientro, $data_uscita) {
            $this->assertTrue($componente->isPersonaInterna());
            $this->assertEquals($componente->getDataEntrataNomadelfia(), $data_rientro);
            $this->assertEquals($componente->getDataUscitaNomadelfia(), $data_uscita);
        });
    }
}
