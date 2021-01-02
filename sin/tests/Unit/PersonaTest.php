<?php

namespace Tests\Unit;

use Tests\TestCase;
use Carbon;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Models\Famiglia;
use App\Nomadelfia\Models\GruppoFamiliare;
use App\Nomadelfia\Models\Posizione;
use App\Nomadelfia\Models\Stato;

class PersonaTest extends TestCase
{


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
        $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato'=>'1','data_entrata_gruppo'=>$data_entrata]);
        $famiglia->componenti()->attach($capoFam->id, ['stato'=>'1', 'posizione_famiglia'=>"CAPO FAMIGLIA", 'data_entrata'=>Carbon::now()->toDatestring()]);

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
        $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato'=>'1','data_entrata_gruppo'=>$data_entrata]);
        $famiglia->componenti()->attach($capoFam->id, ['stato'=>'1', 'posizione_famiglia'=>"CAPO FAMIGLIA", 'data_entrata'=>Carbon::now()->toDatestring()]);

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
        $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato'=>'1','data_entrata_gruppo'=>$data_entrata]);
        $famiglia->componenti()->attach($capoFam->id, ['stato'=>'1', 'posizione_famiglia'=>"CAPO FAMIGLIA", 'data_entrata'=>Carbon::now()->toDatestring()]);

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
        $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato'=>'1','data_entrata_gruppo'=>$data_entrata]);
        $famiglia->componenti()->attach($capoFam->id, ['stato'=>'1', 'posizione_famiglia'=>"CAPO FAMIGLIA", 'data_entrata'=>Carbon::now()->toDatestring()]);

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
        $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato'=>'1','data_entrata_gruppo'=>$data_entrata]);
        $famiglia->componenti()->attach($capoFam->id, ['stato'=>'1', 'posizione_famiglia'=>"CAPO FAMIGLIA", 'data_entrata'=>Carbon::now()->toDatestring()]);

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

    public function testEntrataMinorenneMachioConFamiglia()
    {
        $data_entrata = Carbon::now()->toDatestring();
        $persona = factory(Persona::class)->states("minorenne", "maschio")->create();
        $famiglia = factory(Famiglia::class)->create();

        
        $gruppo = GruppoFamiliare::first();
        $figlio = Posizione::perNome("figlio");
        $nubile = Stato::perNome("celibe");

        $capoFam = factory(Persona::class)->states("maggiorenne", "maschio")->create();
        $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato'=>'1','data_entrata_gruppo'=>$data_entrata]);
        $famiglia->componenti()->attach($capoFam->id, ['stato'=>'1', 'posizione_famiglia'=>"CAPO FAMIGLIA", 'data_entrata'=>Carbon::now()->toDatestring()]);

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
        $this->assertEquals($persona->famigliaAttuale()->data_creazione, Carbon::parse($persona->data_nascita)->addYears(18)->toDatestring());
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
        $this->assertEquals($persona->famigliaAttuale()->data_creazione, Carbon::parse($persona->data_nascita)->addYears(18)->toDatestring());
    }


    public function testEntrataMaggiorenneSposato()
    {
        $data_entrata = Carbon::now()->toDatestring();
        $persona = factory(Persona::class)->states("maggiorenne")->create();
        $gruppo = GruppoFamiliare::first();
        $ospite = Posizione::perNome("ospite");
 
        $persona-> entrataMaggiorenneSposato($data_entrata, $gruppo->id);

        $this->assertTrue($persona->isPersonaInterna());
        $this->assertEquals($persona->getDataEntrataNomadelfia(), $data_entrata);
        $this->assertEquals($persona->posizioneAttuale()->id, $ospite->id);
        $this->assertEquals($persona->posizioneAttuale()->pivot->data_inizio, $data_entrata);
        $this->assertEquals($persona->statoAttuale(), null);
        $this->assertEquals($persona->gruppofamiliareAttuale()->id, $gruppo->id);
        $this->assertEquals($persona->gruppofamiliareAttuale()->pivot->data_entrata_gruppo, $data_entrata);

        $this->assertNull($persona->famigliaAttuale());
    }
}
