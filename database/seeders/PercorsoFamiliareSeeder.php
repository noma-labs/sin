<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneAccoltoAction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * A persona with more famiglie to show the percorso familiare in the profile: born in a famiglia outside Nomadelfia,
 * accolta in a second famiglia she left, and accolta now in a famiglia of Nomadelfia.
 */
final class PercorsoFamiliareSeeder extends Seeder
{
    public function run(): void
    {
        $persona = Persona::factory()->nato(Carbon::now()->subYears(14))->femmina()->create();

        $famigliaDiNascita = $this->famigliaDiConiugi(Carbon::now()->subYears(18));
        $famigliaDiNascita->assegnaFiglioNato($persona, stato: '0');

        $primaAccoglienza = $this->famigliaDiConiugi(Carbon::now()->subYears(25));
        $primaAccoglienza->assegnaFiglioAccolto($persona, stato: '0');

        $famigliaAttuale = $this->famigliaDiConiugi(Carbon::now()->subYears(20), GruppoFamiliare::all()->random());
        app(EntrataMinorenneAccoltoAction::class)->execute($persona, Carbon::now()->subYears(3), $famigliaAttuale);
    }

    /**
     * A famiglia of coniugi, living in Nomadelfia in the gruppo familiare when one is given.
     */
    private function famigliaDiConiugi(Carbon $dataCreazione, ?GruppoFamiliare $gruppo = null): Famiglia
    {
        $capofamiglia = Persona::factory()->maggiorenne()->maschio()->create();
        $moglie = Persona::factory()->maggiorenne()->femmina()->create();
        if ($gruppo instanceof GruppoFamiliare) {
            app(EntrataMaggiorenneConFamigliaAction::class)->execute($capofamiglia, $dataCreazione, $gruppo);
            app(EntrataMaggiorenneConFamigliaAction::class)->execute($moglie, $dataCreazione, $gruppo);
        }

        $famiglia = Famiglia::factory()->coniugi()->create([
            'nome_famiglia' => $capofamiglia->nominativo,
            'data_creazione' => $dataCreazione,
        ]);
        $famiglia->assegnaCapoFamiglia($capofamiglia);
        $famiglia->assegnaMoglie($moglie);

        return $famiglia;
    }
}
