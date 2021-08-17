<?php

use App\Nomadelfia\Models\PopolazioneNomadelfia;
use App\Scuola\Models\Anno;
use App\Scuola\Models\ClasseTipo;
use Carbon\Carbon;
use Illuminate\Database\Seeder;


class ScuolaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createClasseTipo();
        $anno = Anno::createAnno(2021);
        $t = ClasseTipo::all();
        foreach($t as $tipo) {
            if (!$tipo->isSuperiori()){
                $classe = $anno->aggiungiClasse($tipo);
                if ($tipo->isPrescuola()) {
                    $alunni = PopolazioneNomadelfia::figliDaEta(3, 6, "data_nascita", true);
                }else if ($tipo->IsPrimaEl()){
                    $alunni = PopolazioneNomadelfia::figliDaEta(6, 7, "data_nascita",true);
                }else if ($tipo->IsSecondaEl()){
                    $alunni = PopolazioneNomadelfia::figliDaEta(7, 8, "data_nascita",true);
                }else if ($tipo->IsTerzaEl()){
                    $alunni = PopolazioneNomadelfia::figliDaEta(8, 9, "data_nascita",true);
                }else if ($tipo->IsQuartaEl()){
                    $alunni = PopolazioneNomadelfia::figliDaEta(9, 10, "data_nascita",true);
                }else if ($tipo->IsQuintaEl()){
                    $alunni = PopolazioneNomadelfia::figliDaEta(10, 11, "data_nascita",true);
                }else if ($tipo->IsPrimaMed()){
                    $alunni = PopolazioneNomadelfia::figliDaEta(11, 12, "data_nascita",true);
                }else if ($tipo->IsSecondaMed()){
                    $alunni = PopolazioneNomadelfia::figliDaEta(12, 13, "data_nascita",true);
                }else if ($tipo->IsTerzaMed()){
                    $alunni = PopolazioneNomadelfia::figliDaEta(13, 14, "data_nascita",true);
                }else{
                    $alunni = [];
                }
                foreach ($alunni as $a) {
                    $classe->aggiungiAlunno($a->id, Carbon::now());
                }
            }
        }

    }

    public function createClasseTipo(){
        $data = [
            [
                'nome' => 'Prescuola',
                'ord' => 1,
                'ciclo' => 'prescuola'
            ],
            [
                'nome' => '1a Elementare',
                'ord' => 2,
                'ciclo' => 'elementari'

            ],
            [
                'nome' => '2a Elementare',
                'ord' => 3,
                'ciclo' => 'elementari'

            ],
            [
                'nome' => '3a Elementare',
                'ord' => 4,
                'ciclo' => 'elementari'

            ],
            [
                'nome' => '4a Elementare',
                'ord' => 5,
                'ciclo' => 'elementari'

            ],
            [
                'nome' => '5a Elementare',
                'ord' => 6,
                'ciclo' => 'elementari'

            ],
            [
                'nome' => '1a Media',
                'ord' => 7,
                'ciclo' => 'medie'
            ],
            [
                'nome' => '2a Media',
                'ord' => 8,
                'ciclo' => 'medie'
            ],
            [
                'nome' => '3a Media',
                'ord' => 9,
                'ciclo' => 'medie'
            ],
            [
                'nome' => '1 Prof. Agrario',
                'ord' => 10,
                'ciclo' => 'superiori'
            ],
            [
                'nome' => '2 Prof. Agrario',
                'ord' => 12,
                'ciclo' => 'superiori'

            ],
            [
                'nome' => '3 Prof. Agrario',
                'ord' => 13,
                'ciclo' => 'superiori'

            ],
            [
                'nome' => '4 Prof. Agrario',
                'ord' => 14,
                'ciclo' => 'superiori'

            ],
            [
                'nome' => '5 Prof. Agrario',
                'ord' => 15,
                'ciclo' => 'superiori'

            ],
            [
                'nome' => '1 L. Scentifico',
                'ord' => 16,
                'ciclo' => 'superiori'

            ],
            [
                'nome' => '2 L. Scentifico',
                'ord' => 17,
                'ciclo' => 'superiori'

            ],
            [
                'nome' => '3 L. Scentifico',
                'ord' => 18,
                'ciclo' => 'superiori'

            ],
            [
                'nome' => '4 L. Scentifico',
                'ord' => 19,
                'ciclo' => 'superiori'

            ],
            [
                'nome' => '5 L. Scentifico',
                'ord' => 200,
                'ciclo' => 'superiori'

            ],
            [
                'nome' => '1 L. Scienze Umane',
                'ord' => 21,
                'ciclo' => 'superiori'

            ],
            [
                'nome' => '2 L. Scienze Umane',
                'ord' => 22,
                'ciclo' => 'superiori'

            ],
            [
                'nome' => '3 L. Scienze Umane',
                'ord' => 23,
                'ciclo' => 'superiori'

            ],
            [
                'nome' => '4 L. Scienze Umane',
                'ord' => 24,
                'ciclo' => 'superiori'

            ],
            [
                'nome' => '5 L. Scienze Umane',
                'ord' => 25,
                'ciclo' => 'superiori'

            ],
            [
                'nome' => '1 Agrario',
                'ord' => 26,
                'ciclo' => 'superiori'

            ],
            [
                'nome' => '2 Agrario',
                'ord' => 27,
                'ciclo' => 'superiori'

            ],
            [
                'nome' => '3 Agrario',
                'ord' => 28,
                'ciclo' => 'superiori'

            ],
            [
                'nome' => '4 Agrario',
                'ord' =>29,
                'ciclo' => 'superiori'

            ],
            [
                'nome' => '5 Agrario',
                'ord' => 30,
                'ciclo' => 'superiori'

            ],
            [
                'nome' => '1 L. SU.E.S.',
                'ord' => 31,
                'ciclo' => 'superiori'

            ]

        ];
        DB::connection('db_scuola')->table('tipo')->insert($data);
    }
}
