<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Famiglia\Models\TipoFamiglia;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataDallaNascitaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneAccoltoAction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class NomadelfiaTableSeeder extends Seeder
{
    public function run()
    {
        Persona::factory()->count(2000)->create();
        $this->insertFamigliaInPopolazione();
        $this->insertFamiglieSenzaConiuge();
        $this->insertFamiglieFuoriDaNomadelfia();
        $this->insertPersoneSinglePopolazione();
    }

    protected function insertFamigliaInPopolazione(): void
    {
        $gruppo = GruppoFamiliare::all()->random();
        $famiglia = Famiglia::factory()->coniugi()->create();
        $now = Carbon::now();

        DB::connection('db_nomadelfia')->insert('INSERT INTO alfa_enrico_15_feb_23 (FAMIGLIA) values (?)', ['MAMMA1 BABBO1']);
        $last = DB::connection('db_nomadelfia')->select('SELECT LAST_INSERT_ID() as id');
        $capoFam = Persona::factory()->maggiorenne()->maschio()->withIdEnrico($last[0]->id)->create();

        DB::connection('db_nomadelfia')->insert('INSERT INTO alfa_enrico_15_feb_23 (FAMIGLIA) values (?)', ['MAMMA1 BABBO1']);
        $last = DB::connection('db_nomadelfia')->select('SELECT LAST_INSERT_ID() as id');
        $moglie = Persona::factory()->maggiorenne()->femmina()->withIdEnrico($last[0]->id)->create();

        $act = app(EntrataMaggiorenneConFamigliaAction::class);
        $act->execute($capoFam, $now, $gruppo);

        $act = app(EntrataMaggiorenneConFamigliaAction::class);
        $act->execute($moglie, $now, $gruppo);
        $famiglia->assegnaCapoFamiglia($capoFam);
        $famiglia->assegnaMoglie($moglie);
        app(EntrataDallaNascitaAction::class)->execute(Persona::factory()->diEta(3)->femmina()->create(), $famiglia);
        app(EntrataDallaNascitaAction::class)->execute(Persona::factory()->diEta(4)->maschio()->create(), $famiglia);
        app(EntrataMinorenneAccoltoAction::class)->execute(Persona::factory()->diEta(5)->maschio()->create(), Carbon::now()->addYears(4), $famiglia);
        app(EntrataDallaNascitaAction::class)->execute(Persona::factory()->diEta(5)->femmina()->create(), $famiglia);
        app(EntrataDallaNascitaAction::class)->execute(Persona::factory()->diEta(6)->maschio()->create(), $famiglia);
        app(EntrataMinorenneAccoltoAction::class)->execute(Persona::factory()->diEta(7)->maschio()->create(), Carbon::now()->addYears(1), $famiglia);
        app(EntrataDallaNascitaAction::class)->execute(Persona::factory()->diEta(7)->maschio()->create(), $famiglia);
        app(EntrataMinorenneAccoltoAction::class)->execute(Persona::factory()->diEta(8)->femmina()->create(), Carbon::now()->addYears(10), $famiglia);
        app(EntrataMinorenneAccoltoAction::class)->execute(Persona::factory()->diEta(9)->maschio()->create(), Carbon::now()->addYears(5), $famiglia);
        app(EntrataDallaNascitaAction::class)->execute(Persona::factory()->diEta(10)->femmina()->create(), $famiglia);

    }

    /**
     * A famiglia in Nomadelfia for each tipo headed by one person without a coniuge: the three mamme, with a figlio accolto, and the celibe di elezione.
     */
    protected function insertFamiglieSenzaConiuge(): void
    {
        foreach (TipoFamiglia::senzaConiuge() as $tipo) {
            $capofamiglia = Persona::factory()->maggiorenne()->create(['sesso' => $tipo->sessoCapofamiglia()]);
            app(EntrataMaggiorenneSingleAction::class)->execute($capofamiglia, Carbon::now(), GruppoFamiliare::all()->random());
            $famiglia = Famiglia::factory()->create([
                'nome_famiglia' => $capofamiglia->nominativo,
                'tipo' => $tipo,
                'data_creazione' => $tipo->isMamma() ? null : Carbon::now()->subYears(20),
            ]);
            $famiglia->assegnaCapoFamiglia($capofamiglia);

            if ($tipo->isMamma()) {
                app(EntrataMinorenneAccoltoAction::class)->execute(Persona::factory()->diEta(6)->create(), Carbon::now(), $famiglia);
            }
        }
    }

    /**
     * The famiglie of the anagrafe outside Nomadelfia: one of coniugi, one of a vedova whose deceased husband stays outside the nucleo,
     * and one without componenti in the nucleo (listed among the famiglie to fix).
     */
    protected function insertFamiglieFuoriDaNomadelfia(): void
    {
        $famiglia = Famiglia::factory()->coniugi()->create();
        $famiglia->assegnaCapoFamiglia(Persona::factory()->maggiorenne()->maschio()->create());
        $famiglia->assegnaMoglie(Persona::factory()->maggiorenne()->femmina()->create());

        $vedova = Famiglia::factory()->coniugi()->create();
        $vedova->assegnaCapoFamiglia(Persona::factory()->maggiorenne()->maschio()->create(['data_decesso' => Carbon::now()->subYears(5)]), stato: '0');
        $vedova->assegnaMoglie(Persona::factory()->maggiorenne()->femmina()->create());

        $senzaNucleo = Famiglia::factory()->coniugi()->create();
        $capofamiglia = Persona::factory()->maggiorenne()->maschio()->create();
        $senzaNucleo->assegnaCapoFamiglia($capofamiglia);
        $senzaNucleo->uscitaDalNucleoFamiliare($capofamiglia);
    }

    protected function insertPersoneSinglePopolazione(): void
    {
        $act = app(EntrataMaggiorenneSingleAction::class);
        $act->execute(Persona::factory()->maggiorenne()->maschio()->create(), Carbon::now(), GruppoFamiliare::all()->random());
        $act->execute(Persona::factory()->maggiorenne()->femmina()->create(), Carbon::now(), GruppoFamiliare::all()->random());

    }
}
