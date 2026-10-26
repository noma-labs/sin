<?php

declare(strict_types=1);

namespace App\Anagrafe\Famiglia\Actions;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Famiglia\Models\TipoFamiglia;
use App\Anagrafe\Persona\Models\Persona;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

final class CreateMarriageAction
{
    /**
     * Creates the famiglia of the spouses and moves the spouses already in Nomadelfia into the gruppo familiare.
     *
     * @param  int|null  $gruppoId  the gruppo familiare of the new famiglia, required when a spouse is in Nomadelfia
     *
     * @throws Throwable nothing is changed
     */
    public function execute(Persona $husband, Persona $wife, Carbon $data_matrimonio, ?int $gruppoId = null): Famiglia
    {
        return DB::connection('db_nomadelfia')->transaction(function () use ($husband, $wife, $data_matrimonio, $gruppoId): Famiglia {
            $conn = DB::connection('db_nomadelfia');

            $fam = Famiglia::create(['nome_famiglia' => $husband->nominativo, 'tipo' => TipoFamiglia::Coniugi, 'data_creazione' => $data_matrimonio]);

            foreach ([$husband, $wife] as $sposo) {
                // remove from existing active family
                $conn->update("UPDATE famiglie_persone SET stato = '0' WHERE stato = '1' and persona_id = ?", [$sposo->id]);

                if ($gruppoId && $sposo->isPersonaInterna()) {
                    $sposo->spostaNelGruppoFamiliare($gruppoId, $data_matrimonio);
                }
            }

            // assign the husband and the wife in the family
            $conn->insert("INSERT INTO famiglie_persone (famiglia_id, persona_id, posizione_famiglia, stato) VALUES (?, ?, ?, '1')",
                [$fam->id, $husband->id, 'CAPO FAMIGLIA']);
            $conn->insert("INSERT INTO famiglie_persone (famiglia_id, persona_id, posizione_famiglia, stato) VALUES (?, ?, ?, '1')",
                [$fam->id, $wife->id, 'MOGLIE']);

            return $fam;
        });
    }
}
