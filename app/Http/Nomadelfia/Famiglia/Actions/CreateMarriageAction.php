<?php

declare(strict_types=1);

namespace App\Nomadelfia\Famiglia\Actions;

use App\Nomadelfia\Famiglia\Models\Famiglia;
use App\Nomadelfia\Persona\Models\Persona;
use Carbon\Carbon;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;

final class CreateMarriageAction
{
    public function execute(Persona $husband, Persona $wife, Carbon $data_matrimonio): Famiglia
    {
        return DB::connection('db_nomadelfia')->transaction(function (ConnectionInterface $conn) use ($husband, $wife, $data_matrimonio): Famiglia {
            $fam = Famiglia::query()->create(['nome_famiglia' => $husband->nominativo, 'data_creazione' => $data_matrimonio]);

            // remove from existing active family
            $conn->update("UPDATE famiglie_persone SET stato = '0' WHERE stato = '1' and persona_id = ?", [$husband->id]);
            $conn->update("UPDATE famiglie_persone SET stato = '0' WHERE stato = '1' and persona_id = ?", [$wife->id]);

            // remove from gruppo familiari
            $conn->update("UPDATE gruppi_persone SET stato = '0' WHERE stato = '1' and persona_id = ?", [$husband->id]);
            $conn->update("UPDATE gruppi_persone SET stato = '0' WHERE stato = '1' and persona_id = ?", [$wife->id]);

            // assign the husband and the wife in the family
            $conn->insert("INSERT INTO famiglie_persone (famiglia_id, persona_id, posizione_famiglia, stato) VALUES (?, ?, ?, '1')",
                [$fam->id, $husband->id, Famiglia::getCapoFamigliaEnum()]);
            $conn->insert("INSERT INTO famiglie_persone (famiglia_id, persona_id, posizione_famiglia, stato) VALUES (?, ?, ?, '1')",
                [$fam->id, $wife->id, Famiglia::getMoglieEnum()]);

            return $fam;
        });
    }
}
