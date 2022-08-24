<?php

namespace Tests\Unit;

use Domain\Nomadelfia\Incarico\Models\Incarico;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;;
use App\Scuola\Models\Anno;
use App\Scuola\Models\ClasseTipo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

use Tests\MigrateFreshDB;
use Tests\CreatesApplication;

use Domain\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;;
use Domain\Nomadelfia\Azienda\Models\Azienda;
use Carbon;

class PopolazioneTest extends BaseTestCase
{
    use CreatesApplication, MigrateFreshDB;


    public function testDecedutoMaggiorenne()
    {
        $persona = Persona::factory()->maggiorenne()->maschio()->create();

        $data_entrata = Carbon::now()->toDatestring();
        $gruppo = GruppoFamiliare::all()->random();
        $persona->entrataMaggiorenneSingle($data_entrata, $gruppo->id);

        $tot = PopolazioneNomadelfia::totalePopolazione();
        $pop = PopolazioneNomadelfia::popolazione();
        $this->assertEquals($tot, count($pop));

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
        $this->assertEquals($data_decesso,
            $persona->gruppofamiliariStorico()->get()->last()->pivot->data_uscita_gruppo);
        $this->assertNull($persona->famigliaAttuale());
        $this->assertEquals($data_decesso, $persona->famiglieStorico()->get()->last()->pivot->data_uscita);

        $pop = PopolazioneNomadelfia::popolazione();
        $this->assertEquals($tot - 1, count($pop));
    }

    public function testUscitaMaggiorenne()
    {
        $persona = Persona::factory()->maggiorenne()->maschio()->create();

        $data_entrata = Carbon::now()->toDatestring();
        $gruppo = GruppoFamiliare::all()->random();
        $persona->entrataMaggiorenneSingle($data_entrata, $gruppo->id);

        $azienda = Azienda::aziende()->get()->random();
        $persona->assegnaLavoratoreAzienda($azienda, $data_entrata);
        $this->assertEquals(1, $persona->aziendeAttuali()->count());
        // assegna incarico
        $incarico = Incarico::get()->random();
        $persona->assegnaLavoratoreIncarico($incarico, Carbon::now());
        $this->assertEquals(1, $incarico->lavoratoriAttuali()->count());

        $tot = PopolazioneNomadelfia::totalePopolazione();
        $pop = PopolazioneNomadelfia::popolazione();
        $this->assertEquals($tot, count($pop));

        $data_uscita = Carbon::now()->addYears(5)->toDatestring();
        $persona->uscita($data_uscita);

        $this->assertFalse($persona->isPersonaInterna());
        $this->assertEquals($tot - 1, PopolazioneNomadelfia::totalePopolazione());
        $this->assertNull($persona->posizioneAttuale());
        $last_posi = $persona->posizioniStorico()->get()->last();
        $this->assertEquals($data_uscita, $last_posi->pivot->data_fine);
        $celibe = Stato::perNome("celibe");
        $this->assertEquals($persona->statoAttuale()->id, $celibe->id);
        $this->assertNull($persona->gruppofamiliareAttuale());
        $this->assertEquals($data_uscita, $persona->gruppofamiliariStorico()->get()->last()->pivot->data_uscita_gruppo);
        $this->assertNotNull($persona->famigliaAttuale());

        $this->assertEquals(0, $persona->aziendeAttuali()->count());
        $this->assertEquals(1, $azienda->lavoratoriStorici()->count());
        $this->assertCount(0, $incarico->lavoratoriAttuali()->get());
        $this->assertCount(1, $incarico->lavoratoriStorici()->get());

        $pop = PopolazioneNomadelfia::popolazione();
        $this->assertEquals($tot - 1, count($pop));

    }

    /*
    * Testa che quando figlio minorenne esce da nomadelfia,
    * viene tolto da tutte le posizioni con la data di uscita
    * e viene tolto dal nucleo familiare.
    */
    public function testUscitaMinorenne()
    {
        $persona = Persona::factory()->minorenne()->maschio()->create();

        $gruppo = GruppoFamiliare::all()->random();

        $famiglia = Famiglia::factory()->create();
        $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
        $capoFam->gruppifamiliari()->attach($gruppo->id,
            ['stato' => '1', 'data_entrata_gruppo' => Carbon::now()->subYears(10)->toDatestring()]);
        $famiglia->componenti()->attach($capoFam->id,
            ['stato' => '1', 'posizione_famiglia' => "CAPO FAMIGLIA", 'data_entrata' => Carbon::now()->toDatestring()]);

        $persona->entrataNatoInNomadelfia($famiglia->id);

        // assegna minorenne in una classe
        $a = Anno::createAnno(2000);
        $classe = $a->aggiungiClasse(ClasseTipo::all()->random());
        $classe->aggiungiAlunno($persona, \Carbon\Carbon::now());
        $this->assertCount(1, $classe->alunni()->get());

        $tot = PopolazioneNomadelfia::totalePopolazione();
        $pop = PopolazioneNomadelfia::popolazione();
        $this->assertEquals($tot, count($pop));

        $data_uscita = Carbon::now()->addYears(5)->toDatestring();

        $persona->uscita($data_uscita, true);

        $this->assertEquals($tot - 1, PopolazioneNomadelfia::totalePopolazione());
        $this->assertNull($persona->posizioneAttuale());
        $last_posi = $persona->posizioniStorico()->get()->last();
        $this->assertEquals($persona->data_nascita, $last_posi->pivot->data_inizio);
        $this->assertEquals($data_uscita, $last_posi->pivot->data_fine);
        $celibe = Stato::perNome("celibe");
        $this->assertEquals($persona->statoAttuale()->id, $celibe->id);

        $this->assertNull($persona->gruppofamiliareAttuale());
        $this->assertEquals($data_uscita, $persona->gruppofamiliariStorico()->get()->last()->pivot->data_uscita_gruppo);

        $this->assertNull($persona->famigliaAttuale());
        $this->assertEquals($data_uscita, $persona->famiglieStorico()->get()->last()->pivot->data_uscita);

        $this->assertCount(0, $classe->alunni()->get());

        $pop = PopolazioneNomadelfia::popolazione();
        $this->assertEquals($tot - 1, count($pop));
    }

    /*
    * Testa l'uscita di una famiglia.
    */
    public function testUscitaFamiglia()
    {
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

        $capoFam->entrataMaggiorenneSposato($now, $gruppo->id);
        $moglie->entrataMaggiorenneSposato($now, $gruppo->id);
        $famiglia->assegnaCapoFamiglia($capoFam, $now);
        $famiglia->assegnaMoglie($moglie, $now);
        $fnato->entrataNatoInNomadelfia($famiglia->id);
        $faccolto->entrataMinorenneAccolto(Carbon::now()->addYears(2)->toDatestring(), $famiglia->id);

        $this->assertEquals($init_tot + 4, PopolazioneNomadelfia::totalePopolazione());
        $pop = PopolazioneNomadelfia::popolazione();
        $this->assertEquals($init_tot + 4, count($pop));

        $data_uscita = Carbon::now()->toDatestring();
        $famiglia->uscita($data_uscita);

        $this->assertEquals($init_tot, PopolazioneNomadelfia::totalePopolazione());
        $pop = PopolazioneNomadelfia::popolazione();
        $this->assertEquals($init_tot, count($pop));
    }

    /*
    * Testa l'uscita di una famiglia con alcuni componeneti fuori dal nucleo.
    * Controlla che i componenti fuori dal nucleo rimagono come persone interne.
    */
    public function testUscitaFamigliaConComponentiFuoriDalNucleo()
    {
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

        $capoFam->entrataMaggiorenneSposato($now, $gruppo->id);
        $moglie->entrataMaggiorenneSposato($now, $gruppo->id);
        $famiglia->assegnaCapoFamiglia($capoFam, $now);
        $famiglia->assegnaMoglie($moglie, $now);

        $fnato->entrataNatoInNomadelfia($famiglia->id);
        $faccolto->entrataMinorenneAccolto(Carbon::now()->addYears(2)->toDatestring(), $famiglia->id);

        $this->assertEquals($init_tot + 4, PopolazioneNomadelfia::totalePopolazione());
        $pop = PopolazioneNomadelfia::popolazione();
        $this->assertEquals($init_tot + 4, count($pop));

        // toglie un figlio dal nucleo familiare
        $famiglia->uscitaDalNucleoFamiliare($fnato, Carbon::now()->addYears(4)->toDatestring(),
            "test remove from nucleo");

        $data_uscita = Carbon::now()->toDatestring();
        $famiglia->uscita($data_uscita);
        // controlla che il figlio fuori dal nucleo non è uscito
        $this->assertEquals($init_tot + 1, PopolazioneNomadelfia::totalePopolazione());
        $pop = PopolazioneNomadelfia::popolazione();
        $this->assertEquals($init_tot + 1, count($pop));
    }

    /*
   * Testa il conteggio dei figli minorenni nella popolazione
   */
    public function testCountFigliDaEta()
    {
        $now = Carbon::now()->toDatestring();
        $famiglia = Famiglia::factory()->create();
        $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
        $famiglia->assegnaCapoFamiglia($capoFam, $now);
        $gruppo = GruppoFamiliare::all()->random();
        $capoFam->assegnaGruppoFamiliare($gruppo, $now);

        $tot = PopolazioneNomadelfia::totalePopolazione();
        $min = PopolazioneNomadelfia::figliDaEta(0, 18, 'nominativo', null)->count();
        $persona = Persona::factory()->minorenne()->maschio()->create();
        $persona->entrataNatoInNomadelfia($famiglia->id);

        $this->assertEquals($tot + 1, PopolazioneNomadelfia::totalePopolazione());
        $this->assertEquals($min + 1, PopolazioneNomadelfia::figliDaEta(0, 18, 'nominativo', null)->count());

        $mag = PopolazioneNomadelfia::figliDaEta(18, null, 'nominativo', null)->count();
        $persona = Persona::factory()->maggiorenne()->maschio()->create();
        $persona->entrataNatoInNomadelfia($famiglia->id);
        $this->assertEquals($tot + 2, PopolazioneNomadelfia::totalePopolazione());
        $this->assertEquals($min + 1, PopolazioneNomadelfia::figliDaEta(0, 18, 'nominativo', null)->count());
        $this->assertEquals($mag + 1, PopolazioneNomadelfia::figliDaEta(18, null, 'nominativo', null)->count());

    }

    /*
     * Testa quando una persona diventa postulante e nomadelfo effettivo
     */
    public function testNomadelfoEffettivo()
    {
        // entrata maggiorenne
        $data_entrata = Carbon::now()->toDatestring();
        $persona = Persona::factory()->maggiorenne()->maschio()->create();
        $gruppo = GruppoFamiliare::first();
        $persona->entrataMaggiorenneSingle($data_entrata, $gruppo->id);
        $now = Carbon::now()->subYears(4);
        $persona->assegnaPostulante($now);
        $this->assertTrue($persona->posizioneAttuale()->isPostulante());

        $persona->assegnaNomadelfoEffettivo($now->subYears(1));
        $this->assertTrue($persona->posizioneAttuale()->isEffettivo());

    }

    public function testFigliDaEta()
    {
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

        $p0->entrataNatoInNomadelfia($famiglia->id);
        $p1->entrataNatoInNomadelfia($famiglia->id);
        $p2->entrataNatoInNomadelfia($famiglia->id);
        $pafter->entrataNatoInNomadelfia($famiglia->id);

        $this->assertEquals(3, count(PopolazioneNomadelfia::figliDaEta(3, 4, 'nominativo', null, true)));
        $this->assertEquals(2, count(PopolazioneNomadelfia::figliDaEta(3, 4, 'nominativo', null, false)));

        $this->assertEquals(4,
            count(PopolazioneNomadelfia::figliDaEta(2, 4, 'nominativo', null, false))); // - $actualFigli );
        $this->assertEquals(4,
            count(PopolazioneNomadelfia::figliDaEta(2, 4, 'nominativo', null, true))); // - $actualFigli );
    }


    public function testPopolazionePresente()
    {
        $before = PopolazioneNomadelfia::presente()->count();
        $persona = Persona::factory()->maggiorenne()->maschio()->create();

        $data_entrata = Carbon::now()->toDatestring();
        $gruppo = GruppoFamiliare::all()->random();
        $persona->entrataMaggiorenneSingle($data_entrata, $gruppo->id);

        $after = PopolazioneNomadelfia::presente()->count();
        $this->assertEquals(1, $after-$before);

        $c = PopolazioneNomadelfia::presente()->where('id','=' ,$persona->id)->count();
        $this->assertEquals(1, $c);
        $c = PopolazioneNomadelfia::presente()->where('nominativo','=' ,$persona->nominativo)->count();
        $this->assertEquals(1, $c);

        $persona->uscita(Carbon::now()->toDatestring());
        $afterUscita = PopolazioneNomadelfia::presente()->count();
//        dd(PopolazioneNomadelfia::presente()->toSql());
        $this->assertEquals(0, $before-$afterUscita);
    }
}
