<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Anagrafe\Persona\Actions\RegistraDecessoAction;
use App\Anagrafe\Persona\Models\Persona;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

final readonly class DecessoPersonaAction
{
    public function __construct(
        private UscitaPersonaDBAction $uscita,
        private LogDecessoPersonaAction $logDecesso,
        private RegistraDecessoAction $registraDecesso,
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
            $this->registraDecesso->execute($persona, $data_decesso);

            // aggiorna lo stato familiare con la data di decesso
            DB::connection('db_nomadelfia')->insert(
                "UPDATE persone_stati SET data_fine = ?, stato = '0' WHERE persona_id = ? AND stato = '1'",
                [$data_decesso, $persona->id]
            );

            DB::connection('db_nomadelfia')->commit();
        } catch (Exception $e) {
            DB::connection('db_nomadelfia')->rollback();
            throw $e;
        }
    }
}
