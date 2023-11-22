<?php

namespace Domain\Nomadelfia\Famiglia\Actions;

use Carbon\Carbon;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Support\Facades\DB;

class NewMatrimonioAction
{

    public function execute(Persona $husband, Persona $wife, Carbon $data_matrimonio)
    {
        $fam = Famiglia::create(['nome_famiglia' => $husband->nominativo, 'data_creazione' => $data_matrimonio]);

        DB::connection('db_nomadelfia')->beginTransaction();
        try {
            $conn = DB::connection('db_nomadelfia');

            $conn->insert("UPDATE famiglie_persone SET stato = '0' WHERE stato = '1' and persona_id = ?", [$husband->id]);

            $conn->insert("UPDATE famiglie_persone SET stato = '0' WHERE stato = '1' and persona_id = ?", [$husband->id]);
            $conn->insert("UPDATE famiglie_persone SET stato = '0' WHERE stato = '1' and persona_id = ?", [$wife->id]);

            $conn->insert("INSERT INTO famiglie_persone (famiglia_id, persona_id, posizione_famiglia, stato) VALUES (?, ?, ?, '1')",
                [$fam->id, $husband->id, 'CAPO FAMIGLIA']);

            $conn->insert("INSERT INTO famiglie_persone (famiglia_id, persona_id, posizione_famiglia, stato) VALUES (?, ?, ?, '1')",
                [$fam->id, $wife->id, 'MOGLIE']);

            DB::connection('db_nomadelfia')->commit();

        } catch (\Exception $e) {
            DB::connection('db_nomadelfia')->rollback();
            dd($e);
        }

    }
}
