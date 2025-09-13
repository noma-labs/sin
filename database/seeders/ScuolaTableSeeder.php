<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use App\Scuola\Models\Anno;
use App\Scuola\Models\ClasseTipo;
use Carbon\Carbon;
use App\Scuola\Models\Elaborato;
use Illuminate\Database\Seeder;

final class ScuolaTableSeeder extends Seeder
{
    public function run()
    {
        $this->createAnniScolastico();
        $this->createElaborati();
    }
    protected function createAnniScolastico(): void
    {
        $anno = Anno::createAnno(2021);
        $t = ClasseTipo::all();
        foreach ($t as $tipo) {
            if (! $tipo->isSuperiori()) {
                $classe = $anno->aggiungiClasse($tipo);
                $alunni = [];
                if ($tipo->Is3AnniPrescuola()) {
                    $alunni = PopolazioneNomadelfia::figliDaEta(3, 4, 'data_nascita', null, true);
                } elseif ($tipo->Is4AnniPrescuola()) {
                    $alunni = PopolazioneNomadelfia::figliDaEta(4, 5, 'data_nascita', null, true);
                } elseif ($tipo->Is5AnniPrescuola()) {
                    $alunni = PopolazioneNomadelfia::figliDaEta(5, 6, 'data_nascita', null, true);
                } elseif ($tipo->IsPrimaEl()) {
                    $alunni = PopolazioneNomadelfia::figliDaEta(6, 7, 'data_nascita', null, true);
                } elseif ($tipo->IsSecondaEl()) {
                    $alunni = PopolazioneNomadelfia::figliDaEta(7, 8, 'data_nascita', null, true);
                } elseif ($tipo->IsTerzaEl()) {
                    $alunni = PopolazioneNomadelfia::figliDaEta(8, 9, 'data_nascita', null, true);
                } elseif ($tipo->IsQuartaEl()) {
                    $alunni = PopolazioneNomadelfia::figliDaEta(9, 10, 'data_nascita', null, true);
                } elseif ($tipo->IsQuintaEl()) {
                    $alunni = PopolazioneNomadelfia::figliDaEta(10, 11, 'data_nascita', null, true);
                } elseif ($tipo->IsPrimaMed()) {
                    $alunni = PopolazioneNomadelfia::figliDaEta(11, 12, 'data_nascita', null, true);
                } elseif ($tipo->IsSecondaMed()) {
                    $alunni = PopolazioneNomadelfia::figliDaEta(12, 13, 'data_nascita', null, true);
                } elseif ($tipo->IsTerzaMed()) {
                    $alunni = PopolazioneNomadelfia::figliDaEta(13, 14, 'data_nascita', null, true);
                }
                foreach ($alunni as $a) {
                    $classe->aggiungiAlunno($a->id, Carbon::now());
                }
            }
        }
    }

    protected function createElaborati(): void
    {
        Elaborato::factory()->count(20)->create();
    }
}
