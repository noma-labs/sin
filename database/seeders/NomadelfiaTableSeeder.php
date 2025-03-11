<?php

declare(strict_types=1);

namespace Database\Seeders;

use Carbon\Carbon;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataDallaNascitaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneAccoltoAction;
use Illuminate\Database\Seeder;

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

        $capoFam = Persona::factory()->maggiorenne()->maschio()->create();
        $moglie = Persona::factory()->maggiorenne()->femmina()->create();

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
