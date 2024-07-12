<?php

namespace Database\Seeders;

use App\Scuola\Models\Anno;
use App\Scuola\Models\ClasseTipo;
use Carbon\Carbon;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScuolaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createClasseTipo()
            ->createAnnoScolastico();
    }

    protected function createClasseTipo()
    {
        $data = [
            ['id' => 1, 'nome' => ClasseTipo::PRESCUOLA_3ANNI, 'ciclo' => 'prescuola', 'ord' => 1, 'next' => 2],
            ['id' => 2, 'nome' => ClasseTipo::PRESCUOLA_4ANNI, 'ciclo' => 'prescuola', 'ord' => 2, 'next' => 3],
            ['id' => 3, 'nome' => ClasseTipo::PRESCUOLA_5ANNI, 'ciclo' => 'prescuola', 'ord' => 3, 'next' => 4],

            ['id' => 4, 'nome' => ClasseTipo::PRIMA_ELEMENTARE, 'ciclo' => 'elementari', 'ord' => 4, 'next' => 5],
            ['id' => 5, 'nome' => ClasseTipo::SECONDA_ELEMENTARE, 'ciclo' => 'elementari', 'ord' => 5, 'next' => 6],
            ['id' => 6, 'nome' => ClasseTipo::TERZA_ELEMENTARE, 'ciclo' => 'elementari', 'ord' => 6, 'next' => 7],
            ['id' => 7, 'nome' => ClasseTipo::QUARTA_ELEMENTARE, 'ciclo' => 'elementari', 'ord' => 7, 'next' => 8],
            ['id' => 8, 'nome' => ClasseTipo::QUINTA_ELEMENTARE, 'ciclo' => 'elementari', 'ord' => 8, 'next' => 9],
            ['id' => 9, 'nome' => ClasseTipo::PRIMA_MEDIA, 'ciclo' => 'medie', 'ord' => 9, 'next' => 10],
            ['id' => 10, 'nome' => ClasseTipo::SECONDA_MEDIA, 'ciclo' => 'medie', 'ord' => 10, 'next' => 11],
            ['id' => 11, 'nome' => ClasseTipo::TERZA_MEDIA, 'ciclo' => 'medie', 'ord' => 11, 'next' => 12],

            ['id' => 12, 'nome' => '1 superiore', 'ciclo' => 'superiori', 'ord' => 30, 'next' => null],

            ['id' =>13, 'nome' => '1 Prof. Agrario', 'ciclo' => 'superiori', 'ord' => 31, 'next' => 14],
            ['id' => 14, 'nome' => '2 Prof. Agrario', 'ciclo' => 'superiori', 'ord' => 32, 'next' => 15],
            ['id' => 15, 'nome' => '3 Prof. Agrario', 'ciclo' => 'superiori', 'ord' => 33, 'next' => 16],
            ['id' => 16, 'nome' => '4 Prof. Agrario', 'ciclo' => 'superiori', 'ord' => 34, 'next' => 17],
            ['id' => 17, 'nome' => '5 Prof. Agrario', 'ciclo' => 'superiori', 'ord' => 35, 'next' => null],

            ['id' => 18, 'nome' => '1 L. Scientifico', 'ciclo' => 'superiori', 'ord' => 36, 'next' => 19],
            ['id' => 19, 'nome' => '2 L. Scientifico', 'ciclo' => 'superiori', 'ord' => 37, 'next' => 20],
            ['id' => 20, 'nome' => '3 L. Scientifico', 'ciclo' => 'superiori', 'ord' => 38, 'next' => 21],
            ['id' => 21, 'nome' => '4 L. Scientifico', 'ciclo' => 'superiori', 'ord' => 39, 'next' => 22],
            ['id' => 22, 'nome' => '5 L. Scientifico', 'ciclo' => 'superiori', 'ord' => 40, 'next' => null],

            ['id' => 23, 'nome' => '1 L. Scienze Umane', 'ciclo' => 'superiori', 'ord' => 41, 'next' => 24],
            ['id' => 24, 'nome' => '2 L. Scienze Umane', 'ciclo' => 'superiori', 'ord' => 42, 'next' => 25],
            ['id' => 25, 'nome' => '3 L. Scienze Umane', 'ciclo' => 'superiori', 'ord' => 43, 'next' => 26],
            ['id' => 26, 'nome' => '4 L. Scienze Umane', 'ciclo' => 'superiori', 'ord' => 44, 'next' => 27],
            ['id' => 27, 'nome' => '5 L. Scienze Umane', 'ciclo' => 'superiori', 'ord' => 45, 'next' => null],

            ['id' => 28, 'nome' => '1 L. SU.E.S.', 'ciclo' => 'superiori', 'ord' => 51, 'next' => null],
            ['id' => 29, 'nome' => 'Universita', 'ciclo' => 'universita', 'ord' => 70, 'next' => null],

        ];
        DB::connection('db_scuola')->table('tipo')->insert($data);

        return $this;
    }

    protected function createAnnoScolastico()
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

        return $this;
    }
}
