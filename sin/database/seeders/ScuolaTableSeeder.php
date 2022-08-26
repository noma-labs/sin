<?php
namespace Database\Seeders;

use Domain\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use App\Scuola\Models\Anno;
use App\Scuola\Models\ClasseTipo;
use Carbon\Carbon;
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
        $this->createClasseTipo();
        $anno = Anno::createAnno(2021);
        $t = ClasseTipo::all();
        foreach($t as $tipo) {
            if (!$tipo->isSuperiori()){
                $classe = $anno->aggiungiClasse($tipo);
                if ($tipo->IsPrescuola()) {
                    $alunni = PopolazioneNomadelfia::figliDaEta(3, 7, "data_nascita", null, true);
                }else if ($tipo->IsPrimaEl()){
                    $alunni = PopolazioneNomadelfia::figliDaEta(6, 7, "data_nascita", null, true);
                }else if ($tipo->IsSecondaEl()){
                    $alunni = PopolazioneNomadelfia::figliDaEta(7, 8, "data_nascita", null, true);
                }else if ($tipo->IsTerzaEl()){
                    $alunni = PopolazioneNomadelfia::figliDaEta(8, 9, "data_nascita", null, true);
                }else if ($tipo->IsQuartaEl()){
                    $alunni = PopolazioneNomadelfia::figliDaEta(9, 10, "data_nascita", null, true);
                }else if ($tipo->IsQuintaEl()){
                    $alunni = PopolazioneNomadelfia::figliDaEta(10, 11, "data_nascita", null, true);
                }else if ($tipo->IsPrimaMed()){
                    $alunni = PopolazioneNomadelfia::figliDaEta(11, 12, "data_nascita", null, true);
                }else if ($tipo->IsSecondaMed()){
                    $alunni = PopolazioneNomadelfia::figliDaEta(12, 13, "data_nascita", null, true);
                }else if ($tipo->IsTerzaMed()){
                    $alunni = PopolazioneNomadelfia::figliDaEta(13, 14, "data_nascita", null, true);
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
        $data =[
            ['id' =>1, 'nome'=> 'Prescuola', 'ciclo'=>'prescuola', 'ord'=> 1,  'next'=>2],
            ['id' =>2, 'nome'=> '1a Elementare', 'ciclo'=>'elementari', 'ord'=> 10, 'next'=> 3],
            ['id' =>3, 'nome'=> '2a Elementare', 'ciclo'=>'elementari', 'ord'=> 11, 'next'=> 4],
            ['id' =>4, 'nome'=> '3a Elementare', 'ciclo'=>'elementari', 'ord'=> 12, 'next'=> 5],
            ['id' =>5, 'nome'=> '4a Elementare', 'ciclo'=>'elementari', 'ord'=> 13, 'next'=> 6],
            ['id' =>6, 'nome'=> '5a Elementare', 'ciclo'=>'elementari', 'ord'=> 14, 'next'=> 7],
            ['id' =>7, 'nome'=> '1a Media', 'ciclo'=>'medie', 'ord'=> 20, 'next'=> 8],
            ['id' =>8, 'nome'=> '2a Media', 'ciclo'=>'medie', 'ord'=> 21, 'next'=> 9],
            ['id' =>9, 'nome'=> '3a Media', 'ciclo'=>'medie', 'ord'=> 22, 'next'=> 33],
            ['id' =>10,'nome'=>  '1 Prof. Agrario', 'ciclo'=>'superiori', 'ord'=> 31, 'next'=> 11],
            ['id' =>11,'nome'=>  '2 Prof. Agrario', 'ciclo'=>'superiori', 'ord'=> 32, 'next'=> 12],
            ['id' =>12,'nome'=>  '3 Prof. Agrario', 'ciclo'=>'superiori', 'ord'=> 33, 'next'=> 13],
            ['id' =>13,'nome'=>  '4 Prof. Agrario', 'ciclo'=>'superiori', 'ord'=> 34, 'next'=> 14],
            ['id' =>14,'nome'=>  '5 Prof. Agrario', 'ciclo'=>'superiori', 'ord'=> 35, 'next'=> NULL],
            ['id' =>15,'nome'=>  '1 L. Scientifico', 'ciclo'=>'superiori', 'ord'=> 36, 'next'=> 16],
            ['id' =>16,'nome'=>  '2 L. Scientifico', 'ciclo'=>'superiori', 'ord'=> 37, 'next'=> 17],
            ['id' =>17,'nome'=>  '3 L. Scientifico', 'ciclo'=>'superiori', 'ord'=> 38, 'next'=> 18],
            ['id' =>18,'nome'=>  '4 L. Scientifico', 'ciclo'=>'superiori', 'ord'=> 39, 'next'=> 19],
            ['id' =>19,'nome'=>  '5 L. Scientifico', 'ciclo'=>'superiori', 'ord'=> 40, 'next'=> NULL],
            ['id' =>20,'nome'=>  '1 L. Scienze Umane', 'ciclo'=>'superiori', 'ord'=> 41, 'next'=> 21],
            ['id' =>21,'nome'=>  '2 L. Scienze Umane', 'ciclo'=>'superiori', 'ord'=> 42, 'next'=> 22],
            ['id' =>22,'nome'=>  '3 L. Scienze Umane', 'ciclo'=>'superiori', 'ord'=> 43, 'next'=> 23],
            ['id' =>23,'nome'=>  '4 L. Scienze Umane', 'ciclo'=>'superiori', 'ord'=> 44, 'next'=> 24],
            ['id' =>24,'nome'=>  '5 L. Scienze Umane', 'ciclo'=>'superiori', 'ord'=> 45, 'next'=> NULL],
            ['id' =>30,'nome'=>  '1 L. SU.E.S.', 'ciclo'=>'superiori', 'ord'=> 51, 'next'=> NULL],
            ['id' =>31,'nome'=>  'Universita', 'ciclo'=>'universita', 'ord'=> 70, 'next'=> NULL],
            ['id' =>32,'nome'=>  '1a Classe', 'ciclo'=>'elementari', 'ord'=> 46, 'next'=>NULL ],
            ['id' =>33,'nome'=>  '1 superiore', 'ciclo'=>'superiori', 'ord'=> 30, 'next'=> NULL]
        ];
        DB::connection('db_scuola')->table('tipo')->insert($data);
    }
}
