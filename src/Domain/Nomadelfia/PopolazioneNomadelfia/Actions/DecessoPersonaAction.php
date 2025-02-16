<?php

declare(strict_types=1);

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use Carbon\Carbon;
use Domain\Nomadelfia\Persona\Models\Persona;
use Exception;
use Illuminate\Support\Facades\DB;

final class DecessoPersonaAction
{
    public function __construct(
        private UscitaPersonaDBAction $uscita,
        private LogDecessoPersonaAction $logDecesso,
    ) {}

    public function execute(Persona $persona, Carbon $data_decesso): void
    {
        $this->uscita->execute($persona, $data_decesso);
        $this->deceduto($persona, $data_decesso);

        $this->logDecesso->execute(
            $persona,
            $data_decesso,
        );
    }

    public function deceduto(Persona $persona, $data_decesso): void
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
