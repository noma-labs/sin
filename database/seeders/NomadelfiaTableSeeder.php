<?php

declare(strict_types=1);

namespace Database\Seeders;

use Carbon\Carbon;
use App\Nomadelfia\Famiglia\Models\Famiglia;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataDallaNascitaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneAccoltoAction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class NomadelfiaTableSeeder extends Seeder
{
    public function run()
    {
        $this->insertFamigliaInPopolazione();
        $this->insertPersoneSinglePopolazione();
    }

    protected function insertFamigliaInPopolazione(): void
    {
        $gruppo = GruppoFamiliare::all()->random();
        $famiglia = Famiglia::factory()->create();
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

    protected function insertPersoneSinglePopolazione(): void
    {
        $act = app(EntrataMaggiorenneSingleAction::class);
        $act->execute(Persona::factory()->maggiorenne()->maschio()->create(), Carbon::now(), GruppoFamiliare::all()->random());
        $act->execute(Persona::factory()->maggiorenne()->femmina()->create(), Carbon::now(), GruppoFamiliare::all()->random());

    }
}
