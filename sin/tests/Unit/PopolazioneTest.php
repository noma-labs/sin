<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

use Tests\MigrateFreshDB;
use Tests\CreatesApplication;

use App\Nomadelfia\Models\PopolazioneNomadelfia;
use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Models\GruppoFamiliare;
use App\Nomadelfia\Models\Famiglia;
use App\Nomadelfia\Models\Stato;
use Carbon;

class PopolazioneTest extends BaseTestCase
{
    use CreatesApplication, MigrateFreshDB;

    public function testDecedutoMaggiorenne()
    {
        $persona = factory(Persona::class)
                    ->states("maggiorenne", "maschio")
                    ->create();
        
        $data_entrata = Carbon::now()->toDatestring();
        $gruppo = GruppoFamiliare::all()->random();
        $persona->entrataMaggiorenneSingle($data_entrata, $gruppo->id);

        $tot = PopolazioneNomadelfia::totalePopolazione();

        $data_decesso = Carbon::now()->addYears(5)->toDatestring();
        $persona->deceduto($data_decesso);

        $persona = Persona::findOrFail($persona->id);
        $this->assertTrue($persona->isDeceduto());
        $this->assertFalse($persona->isPersonaInterna());
        $this->assertEquals($tot - 1, PopolazioneNomadelfia::totalePopolazione());
        $this->assertNull($persona->posizioneAttuale());
        $this->assertEquals($data_decesso, $persona->posizioniStorico()->get()->last()->pivot->data_fine);
        $this->assertNull($persona->statoAttuale());
        $this->assertEquals($data_decesso, $persona->statiStorico()->get()->last()->pivot->data_fine);
        $this->assertNull($persona->gruppofamiliareAttuale());
        $this->assertEquals($data_decesso, $persona->gruppofamiliariStorico()->get()->last()->pivot->data_uscita_gruppo);
        $this->assertNull($persona->famigliaAttuale());
        $this->assertEquals($data_decesso, $persona->famiglieStorico()->get()->last()->pivot->data_uscita);
    }

    public function testUscitaMaggiorenne()
    {
        $persona = factory(Persona::class)
                    ->states("maggiorenne", "maschio")
                    ->create();
        
        $data_entrata = Carbon::now()->toDatestring();
        $gruppo = GruppoFamiliare::all()->random();
        $persona->entrataMaggiorenneSingle($data_entrata, $gruppo->id);

        $tot = PopolazioneNomadelfia::totalePopolazione();

        $data_uscita = Carbon::now()->addYears(5)->toDatestring();
        $persona->uscita($data_uscita);

        $this->assertFalse($persona->isPersonaInterna());
        $this->assertEquals($tot - 1, PopolazioneNomadelfia::totalePopolazione());
        $this->assertNull($persona->posizioneAttuale());
        $last_posi =  $persona->posizioniStorico()->get()->last();
        $this->assertEquals($data_uscita, $last_posi->pivot->data_fine);
        $celibe = Stato::perNome("celibe");
        $this->assertEquals($persona->statoAttuale()->id, $celibe->id);
        $this->assertNull($persona->gruppofamiliareAttuale());
        $this->assertEquals($data_uscita, $persona->gruppofamiliariStorico()->get()->last()->pivot->data_uscita_gruppo);
        $this->assertNotNull($persona->famigliaAttuale());
    }

    /*
    * Testa che quando figlio minorenne esce da nomadelfia,
    * viene tolto da tutte le posizioni con la data di uscita
    * e viene tolto dal nucleo familiare.
    */
    public function testUscitaMinorenne()
    {
        $persona = factory(Persona::class)->states("minorenne", "maschio")->create();
         
        $data_entrata = Carbon::now()->toDatestring();
        $gruppo = GruppoFamiliare::all()->random();

        $famiglia = factory(Famiglia::class)->create();
        $capoFam = factory(Persona::class)->states("maggiorenne", "maschio")->create();
        $capoFam->gruppifamiliari()->attach($gruppo->id, ['stato'=>'1','data_entrata_gruppo'=> Carbon::now()->subYears(10)->toDatestring()]);
        $famiglia->componenti()->attach($capoFam->id, ['stato'=>'1', 'posizione_famiglia'=>"CAPO FAMIGLIA", 'data_entrata'=>Carbon::now()->toDatestring()]);

        $persona->entrataNatoInNomadelfia($famiglia->id);

        $tot = PopolazioneNomadelfia::totalePopolazione();
        $data_uscita = Carbon::now()->addYears(5)->toDatestring();

        $persona->uscita($data_uscita, TRUE);

        $this->assertEquals($tot - 1, PopolazioneNomadelfia::totalePopolazione());
        $this->assertNull($persona->posizioneAttuale());
        $last_posi =   $persona->posizioniStorico()->get()->last();
        $this->assertEquals($persona->data_nascita, $last_posi->pivot->data_inizio);
        $this->assertEquals($data_uscita, $last_posi->pivot->data_fine);
        $celibe = Stato::perNome("celibe");
        $this->assertEquals($persona->statoAttuale()->id, $celibe->id);
         
        $this->assertNull($persona->gruppofamiliareAttuale());
        $this->assertEquals($data_uscita, $persona->gruppofamiliariStorico()->get()->last()->pivot->data_uscita_gruppo);
        
        $this->assertNull($persona->famigliaAttuale());
        $this->assertEquals($data_uscita, $persona->famiglieStorico()->get()->last()->pivot->data_uscita);
    }

    /*
    * Testa l'uscita di una famiglia.
    */
    public function testUscitaFamiglia()
    {
        $init_tot = PopolazioneNomadelfia::totalePopolazione();
        $now = Carbon::now()->toDatestring();
        $gruppo = GruppoFamiliare::all()->random();

        $famiglia = factory(Famiglia::class)->create();

        $capoFam = factory(Persona::class)->states("maggiorenne", "maschio")->create();
        $moglie = factory(Persona::class)->states("maggiorenne", "femmina")->create();
        $fnato = factory(Persona::class)->states("minorenne", "femmina")->create();
        $faccolto = factory(Persona::class)->states("minorenne", "maschio")->create();

        $capoFam->entrataMaggiorenneSposato($now, $gruppo->id);
        $moglie->entrataMaggiorenneSposato($now, $gruppo->id);
        $famiglia->assegnaCapoFamiglia($capoFam, $now);
        $famiglia->assegnaMoglie($moglie, $now);
        $fnato->entrataNatoInNomadelfia($famiglia->id);
        $faccolto->entrataMinorenneAccolto(Carbon::now()->addYears(2)->toDatestring(), $famiglia->id);

        $this->assertEquals($init_tot + 4, PopolazioneNomadelfia::totalePopolazione());

        $data_uscita = Carbon::now()->toDatestring();
        $famiglia->uscita($data_uscita);
        
        $this->assertEquals($init_tot, PopolazioneNomadelfia::totalePopolazione());
    }

    /*
    * Testa l'uscita di una famiglia con alcuni componeneti fuori dal nucleo.
    * Controlla che i componenti fuori dal nucleo rimagono come persone interne.
    */
    public function testUscitaFamigliaConComponentiFuoriDalNucleo()
    {
        $init_tot = PopolazioneNomadelfia::totalePopolazione();
        $now = Carbon::now()->toDatestring();
        $gruppo = GruppoFamiliare::all()->random();

        $famiglia = factory(Famiglia::class)->create();

        $capoFam = factory(Persona::class)->states("maggiorenne", "maschio")->create();
        $moglie = factory(Persona::class)->states("maggiorenne", "femmina")->create();
        $fnato = factory(Persona::class)->states("minorenne", "femmina")->create();
        $faccolto = factory(Persona::class)->states("minorenne", "maschio")->create();

        $capoFam->entrataMaggiorenneSposato($now, $gruppo->id);
        $moglie->entrataMaggiorenneSposato($now, $gruppo->id);
        $famiglia->assegnaCapoFamiglia($capoFam, $now);
        $famiglia->assegnaMoglie($moglie, $now);

        $fnato->entrataNatoInNomadelfia($famiglia->id);
        $faccolto->entrataMinorenneAccolto(Carbon::now()->addYears(2)->toDatestring(), $famiglia->id);

        $this->assertEquals($init_tot + 4, PopolazioneNomadelfia::totalePopolazione());
        // toglie un figlio dal nucleo familiare
        $famiglia->uscitaDalNucleoFamiliare($fnato, Carbon::now()->addYears(4)->toDatestring(), "test remove from nucleo");

        $data_uscita = Carbon::now()->toDatestring();
        $famiglia->uscita($data_uscita);
        // controlla che il figlio fuori dal nucleo non è uscito
        $this->assertEquals($init_tot + 1, PopolazioneNomadelfia::totalePopolazione());
    }
}
