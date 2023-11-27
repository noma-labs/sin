<?php

namespace Domain\Nomadelfia\Famiglia\Actions;

use Carbon\Carbon;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Support\Facades\DB;

class CreateMarriageAction
{
    public function execute(Persona $husband, Persona $wife, Carbon $data_matrimonio)
    {
        // create a new family
        $fam = Famiglia::create(['nome_famiglia' => $husband->nominativo, 'data_creazione' => $data_matrimonio]);

        DB::connection('db_nomadelfia')->beginTransaction();
        try {
            $conn = DB::connection('db_nomadelfia');

            // remove from existing active family
            $conn->insert("UPDATE famiglie_persone SET stato = '0' WHERE stato = '1' and persona_id = ?", [$husband->id]);
            $conn->insert("UPDATE famiglie_persone SET stato = '0' WHERE stato = '1' and persona_id = ?", [$wife->id]);

            // remove from gruppo familiari
            $conn->insert("UPDATE gruppi_persone SET stato = '0' WHERE stato = '1' and persona_id = ?", [$husband->id]);
            $conn->insert("UPDATE gruppi_persone SET stato = '0' WHERE stato = '1' and persona_id = ?", [$wife->id]);

            // assign the husband and the wife in the family
            $conn->insert("INSERT INTO famiglie_persone (famiglia_id, persona_id, posizione_famiglia, stato) VALUES (?, ?, ?, '1')",
                [$fam->id, $husband->id, 'CAPO FAMIGLIA']);
            $conn->insert("INSERT INTO famiglie_persone (famiglia_id, persona_id, posizione_famiglia, stato) VALUES (?, ?, ?, '1')",
                [$fam->id, $wife->id, 'MOGLIE']);

            DB::connection('db_nomadelfia')->commit();

        } catch (\Exception $e) {
            DB::connection('db_nomadelfia')->rollback();
            dd($e);
        }
        return $fam;
    }
}
