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
            $classe = $anno->aggiungiClasse($tipo);
            if ($tipo->isPrescuola()) {
                $alunni = PopolazioneNomadelfia::figliDaEta(3, 7, "data_nascita");
            }else if ($tipo->IsPrimaEl()){
                $alunni = PopolazioneNomadelfia::figliDaEta(6, 7, "data_nascita");
            }else if ($tipo->IsSecondaEl()){
                $alunni = PopolazioneNomadelfia::figliDaEta(7, 8, "data_nascita");
            }else if ($tipo->IsTerzaEl()){
                $alunni = PopolazioneNomadelfia::figliDaEta(8, 9, "data_nascita");
            }else if ($tipo->IsQuartaEl()){
                $alunni = PopolazioneNomadelfia::figliDaEta(9, 10, "data_nascita");
            }else if ($tipo->IsQuintaEl()){
                $alunni = PopolazioneNomadelfia::figliDaEta(10, 11, "data_nascita");
            }else if ($tipo->IsPrimaMed()){
                $alunni = PopolazioneNomadelfia::figliDaEta(11, 12, "data_nascita");
            }else if ($tipo->IsSecondaMed()){
                $alunni = PopolazioneNomadelfia::figliDaEta(12, 13, "data_nascita");
            }else if ($tipo->IsTerzaMed()){
                $alunni = PopolazioneNomadelfia::figliDaEta(13, 14, "data_nascita");
            }else{
                $alunni = [];
            }
            foreach ($alunni as $a) {
                $classe->aggiungiAlunno($a->id, Carbon::now());
            }
        }

    }

    public function createClasseTipo(){
        $data = [
            [
                'nome' => 'Prescuola',
                'ord' => 1,
            ],
            [
                'nome' => '1a Elementare',
                'ord' => 2,
            ],
            [
                'nome' => '2a Elementare',
                'ord' => 3,

            ],
            [
                'nome' => '3a Elementare',
                'ord' => 4,

            ],
            [
                'nome' => '4a Elementare',
                'ord' => 5,

            ],
            [
                'nome' => '4a Elementare',
                'ord' => 6,

            ],
            [
                'nome' => '4a Elementare',
                'ord' => 7,

            ],
            [
                'nome' => '5a Elementare',
                'ord' => 8,

            ],
            [
                'nome' => '1a Media',
                'ord' => 9,

            ],
            [
                'nome' => '2a Media',
                'ord' => 10,

            ],
            [
                'nome' => '3a Media',
                'ord' => 11,

            ],
            [
                'nome' => '1 Agrario',
                'ord' => 12,

            ],
            [
                'nome' => '1 Agrario',
                'ord' => 13,

            ],
            [
                'nome' => '2 Agrario',
                'ord' => 14,

            ],
            [
                'nome' => '3 Agrario',
                'ord' => 15,

            ],
            [
                'nome' => '4 Agrario',
                'ord' => 16,

            ],
            [
                'nome' => '5 Agrario',
                'ord' => 17,

            ],
            [
                'nome' => '1 Scentifico',
                'ord' => 18,

            ],
            [
                'nome' => '2 Scentifico',
                'ord' => 19,

            ],
            [
                'nome' => '3 Scentifico',
                'ord' => 20,

            ],
            [
                'nome' => '4 Scentifico',
                'ord' => 21,

            ],
            [
                'nome' => '5 Scentifico',
                'ord' => 22,

            ],
            [
                'nome' => '1 Scienze Umane',
                'ord' => 23,

            ],
            [
                'nome' => '2 Scienze Umane',
                'ord' => 24,

            ],
            [
                'nome' => '3 Scienze Umane',
                'ord' => 25,

            ],
            [
                'nome' => '4 Scienze Umane',
                'ord' => 26,

            ],
            [
                'nome' => '5 Scienze Umane',
                'ord' => 27,

            ],
            [
                'nome' => '1 Agrario',
                'ord' => 28,

            ],
            [
                'nome' => '2 Agrario',
                'ord' => 29,

            ],
            [
                'nome' => '3 Agrario',
                'ord' => 30,

            ],
            [
                'nome' => '4 Agrario',
                'ord' =>31,

            ],
            [
                'nome' => '5 Agrario',
                'ord' => 32,

            ]

        ];
        DB::connection('db_scuola')->table('tipo')->insert($data);
    }
}
