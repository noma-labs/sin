<?php

namespace App\Nomadelfia\Actions;

use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;;
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

        $persona_id = $persona->id;

        DB::connection('db_nomadelfia')->beginTransaction();
        try {
            $conn = DB::connection('db_nomadelfia');

            $conn->insert(
                "INSERT INTO popolazione (persona_id, data_entrata) VALUES (?, ?)",
                [$persona_id, $data_entrata]
            );

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