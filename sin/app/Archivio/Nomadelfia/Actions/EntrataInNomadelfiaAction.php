<?php

namespace App\Nomadelfia\Actions;

use App\Nomadelfia\Models\Categoria;
use App\Nomadelfia\Models\Famiglia;
use App\Nomadelfia\Models\GruppoFamiliare;
use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Models\Posizione;
use App\Nomadelfia\Models\Stato;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EntrataInNomadelfiaAction
{

    public function execute(
        Persona $persona,
        $data_entrata,
        Posizione $posizione,
        $posizione_data,
        GruppoFamiliare $gruppoFamiliare,
        $gruppo_data,
        Stato $stato = null,
        $stato_data = null,
        Famiglia $famiglia = null,
        $famiglia_posizione = null,
        $famiglia_data = null
    ) {
        // TODO: se la persona esiste già nella tabella popolazione e la data di fine a null, allora fail
        if ($persona->isPersonaInterna()) {
            throw new \Exception("Impossibile inserire `{$persona->nominativo}` come prima volta nella comunita. Risulta essere già stata inserita.");
        }

        $interna = Categoria::perNome("interno");
        $esterno = Categoria::perNome("esterno");
        $persona_id = $persona->id;

        DB::connection('db_nomadelfia')->beginTransaction();
        try {
            $conn = DB::connection('db_nomadelfia');

            // se la persona era esterna (rientrata in Nomadelfia) concludi la categoria da esterna con la data di entrata
            $conn->update(
                "UPDATE persone SET stato = '1' WHERE id = ? and stato = '0';",
                [$persona_id]
            );

            $conn->insert(
                "INSERT INTO popolazione (persona_id, data_entrata) VALUES (?, ?)",
                [$persona_id, $data_entrata]
            );

//            // inserisce la persona nella popolazione (mette la categoria persona interna)
//            $conn->insert(
//                "INSERT INTO persone_categorie (persona_id, categoria_id, data_inizio, stato, created_at, updated_at) VALUES (?, ?, ?, 1, NOW(), NOW())",
//                [$persona_id, $interna->id, $data_entrata]
//            );
//
//            // se la persona era esterna (rientrata in Nomadelfia) concludi la categoria da esterna con la data di entrata
//            $conn->update(
//                "UPDATE persone_categorie SET data_fine=?, stato = '0' WHERE persona_id = ? and categoria_id = ? and data_fine IS NULL;",
//                [$data_entrata, $persona_id, $esterno->id]
//            );

            // inserisce la persone come Ospite, o Figlio
            $conn->insert(
                "INSERT INTO persone_posizioni (persona_id, posizione_id, data_inizio, stato) VALUES (?, ?, ?,'1')",
                [$persona_id, $posizione->id, $posizione_data]
            );

            // inserisce la persona nel gruppo familiare
            $conn->insert(
                "INSERT INTO gruppi_persone (gruppo_famigliare_id, persona_id, data_entrata_gruppo, stato) VALUES (?, ?, ?, '1')",
                [$gruppoFamiliare->id, $persona_id, $gruppo_data]
            );

            if ($stato) {
                // inserisce lo stato familiare di Celibe o Nubile
                // NOTE: ignora perchè lo stato di celibe o nubile rimane
                $conn->insert(
                    "INSERT IGNORE INTO persone_stati (persona_id, stato_id, data_inizio, stato) VALUES (?, ?, ?,'1')",
                    [$persona_id, $stato->id, $stato_data]
                );
            }

            // inserisce la persona nella famiglia con una posizione
            if ($famiglia) {
                // NOTE: se la persone è già nella famiglia (è duplicato) si aggiorna lo stato a 1 con la nuova data
                $conn->insert("INSERT INTO famiglie_persone (famiglia_id, persona_id, data_entrata, posizione_famiglia, stato) VALUES (?, ?, ?, ?, '1')  ON DUPLICATE KEY UPDATE stato='1', data_entrata=?, data_uscita=NULL",
                    [$famiglia->id, $persona_id, $famiglia_data, $famiglia_posizione, $famiglia_data]
                );
            }
            DB::connection('db_nomadelfia')->commit();
        } catch (\Exception $e) {
            DB::connection('db_nomadelfia')->rollback();
            dd($e);
        }
    }


}