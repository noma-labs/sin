<?php

namespace App\Nomadelfia\Actions;

use App\Nomadelfia\Models\Azienda;
use App\Nomadelfia\Models\Famiglia;
use App\Nomadelfia\Models\GruppoFamiliare;
use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Models\Posizione;
use App\Nomadelfia\Models\Stato;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DeleteIncaricoAction
{

    public function execute(Azienda $azienda, $data_fine) {

        $azienda->delete()


        foreach ($azienda->lavoratoriAttuali() as $lavoratore) {
            $value = $value * 2;
        }

        DB::connection('db_nomadelfia')->beginTransaction();
        try {
            $conn = DB::connection('db_nomadelfia');

                $conn->
            }
            DB::connection('db_nomadelfia')->commit();
        } catch (\Exception $e) {
            DB::connection('db_nomadelfia')->rollback();
            dd($e);
        }
    }


}