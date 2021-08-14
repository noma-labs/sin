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
}
