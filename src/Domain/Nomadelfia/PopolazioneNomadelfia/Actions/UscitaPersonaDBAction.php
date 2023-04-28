<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\UscitaPersonaData;
use Illuminate\Support\Facades\DB;

class UscitaPersonaDBAction
{
    public function __construct()
    {
    }

    /*
    * Fa uscire una persona da Nomadelfia aggiornando tutte le posizioni attuali con la data di uscita.
    * Se disable_from_family è True e se è un minorenne, la persona viene anche messa fuori dal nucleo familiare.
    */
    public function execute(Persona $persona, string $data_uscita, bool $disableFromFamily = false)
    {
        $dto = new UscitaPersonaData();
        $dto->persona = $persona;
        $dto->data_uscita = $data_uscita;
        $dto->disableFromFamily = $disableFromFamily;
        $this->save($dto);
    }

    public function save(UscitaPersonaData $uscitaPersonaData)
    {
        // TODO: if the persona is not in the popolazione, fail with an expcetion
        $persona_id = $uscitaPersonaData->persona->id;

        DB::connection('db_nomadelfia')->beginTransaction();
        try {
            $conn = DB::connection('db_nomadelfia');

            // setta la data uscita della persona
            $conn->insert('UPDATE popolazione SET data_uscita = ? WHERE persona_id = ? AND data_uscita IS NULL',
                [$uscitaPersonaData->data_uscita, $persona_id]);

            // conclude la posizione in nomadelfia della persona con la data di uscita
            $conn->insert(
                "UPDATE persone_posizioni SET data_fine = ?, stato = '0' WHERE persona_id = ? AND stato = '1'",
                [$uscitaPersonaData->data_uscita, $persona_id]
            );

            // conclude la persona nel gruppo familiare con la data di uscita
            $conn->insert(
                "UPDATE gruppi_persone SET data_uscita_gruppo = ?, stato = '0' WHERE persona_id = ? AND stato = '1'",
                [$uscitaPersonaData->data_uscita, $persona_id]
            );

            // conclude le aziende dove lavora con la data di uscita
            $conn->update(
                "UPDATE aziende_persone SET data_fine_azienda = ?, stato = 'Non Attivo' WHERE persona_id = ? AND stato = 'Attivo'",
                [$uscitaPersonaData->data_uscita, $persona_id]
            );

            $conn->update(
                'UPDATE incarichi_persone SET data_fine = ?  WHERE persona_id = ? AND data_fine IS NULL',
                [$uscitaPersonaData->data_uscita, $persona_id]
            );

            // conclude la scuola
            $conn->update(
                'UPDATE db_scuola.alunni_classi SET data_fine = ?  WHERE persona_id = ? AND data_fine IS NULL',
                [$uscitaPersonaData->data_uscita, $persona_id]
            );

            if (! $uscitaPersonaData->persona->isMaggiorenne() && $uscitaPersonaData->disableFromFamily) {
                // toglie la persona dal nucleo familiare
                $conn->insert(
                    "UPDATE famiglie_persone  SET data_uscita = ?, stato = '0' WHERE persona_id = ? AND stato = '1'",
                    [$uscitaPersonaData->data_uscita, $persona_id]
                );
            }

            DB::connection('db_nomadelfia')->commit();
        } catch (\Exception $e) {
            DB::connection('db_nomadelfia')->rollback();
            throw $e;
        }
    }
}
