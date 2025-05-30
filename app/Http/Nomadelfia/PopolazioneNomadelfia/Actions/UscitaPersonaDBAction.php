<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\UscitaPersonaData;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

final class UscitaPersonaDBAction
{
    /*
    * Fa uscire una persona da Nomadelfia aggiornando tutte le posizioni attuali con la data di uscita.
    * Se disable_from_family è True e se è un minorenne, la persona viene anche messa fuori dal nucleo familiare.
    */
    public function execute(Persona $persona, Carbon $data_uscita, bool $disableFromFamily = false): void
    {
        $dto = new UscitaPersonaData;
        $dto->persona = $persona;
        $dto->data_uscita = $data_uscita;
        $dto->disableFromFamily = $disableFromFamily;
        $this->save($dto);
    }

    public function save(UscitaPersonaData $uscitaPersonaData): void
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

            if ($uscitaPersonaData->persona->isFiglio() && $uscitaPersonaData->disableFromFamily) {
                // caso: un figlio che esce dalla comunità da solo, deve essere tolto dal nucleo familiare.
                //       Invece, un figlio che esce con la famiglia deve rimanere nel nucleo familiare d'origine.
                $conn->insert(
                    "UPDATE famiglie_persone  SET stato = '0' WHERE persona_id = ? AND stato = '1'",
                    [$persona_id]
                );
            }

            DB::connection('db_nomadelfia')->commit();
        } catch (Exception $e) {
            DB::connection('db_nomadelfia')->rollback();
            throw $e;
        }

    }
}
