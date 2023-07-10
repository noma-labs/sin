<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use Domain\Nomadelfia\Persona\Models\Persona;
use Exception;
use Illuminate\Support\Facades\DB;

class DecessoPersonaAction
{
    private LogDecessoPersonaAction $logDecesso;

    private UscitaPersonaDBAction $uscita;

    private SendEmailPersonaDecessoAction $email;

    public function __construct(
        UscitaPersonaDBAction         $uscita,
        LogDecessoPersonaAction       $logDecesso,
        SendEmailPersonaDecessoAction $email
    )
    {
        $this->uscita = $uscita;
        $this->logDecesso = $logDecesso;
        $this->email = $email;
    }

    public function execute(Persona $persona, string $data_decesso)
    {
        $this->uscita->execute($persona, $data_decesso);
        $this->deceduto($persona, $data_decesso);

        $this->logDecesso->execute(
            $persona,
            $data_decesso,
        );

        $this->email->execute(
            $persona,
            $data_decesso
        );
    }

    public function deceduto(Persona $persona, $data_decesso)
    {
        DB::connection('db_nomadelfia')->beginTransaction();
        try {
            $conn = DB::connection('db_nomadelfia');

            // aggiorna la data di decesso
            $conn->update(
                'UPDATE persone SET data_decesso = ?, updated_at = NOW() WHERE id = ?',
                [$data_decesso, $persona->id]
            );

            // aggiorna lo stato familiare con la data di decesso
            $conn->insert(
                "UPDATE persone_stati SET data_fine = ?, stato = '0' WHERE persona_id = ? AND stato = '1'",
                [$data_decesso, $persona->id]
            );

            // aggiorna la data di uscita dalla famiglia con la data di decesso
            $conn->insert(
                "UPDATE famiglie_persone SET stato = '0' WHERE persona_id = ? AND stato = '1'", [$persona->id]
            );

            DB::connection('db_nomadelfia')->commit();
        } catch (Exception $e) {
            DB::connection('db_nomadelfia')->rollback();
            throw $e;
        }
    }
}
