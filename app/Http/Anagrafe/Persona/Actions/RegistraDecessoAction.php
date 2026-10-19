<?php

declare(strict_types=1);

namespace App\Anagrafe\Persona\Actions;

use App\Anagrafe\Persona\Models\Persona;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Registra nell'anagrafe il decesso di una persona: la data di decesso e l'uscita dalla famiglia attuale.
 */
final class RegistraDecessoAction
{
    public function execute(Persona $persona, Carbon|string $data_decesso): void
    {
        $conn = DB::connection('db_nomadelfia');

        $conn->update(
            'UPDATE persone SET data_decesso = ?, updated_at = NOW() WHERE id = ?',
            [$data_decesso, $persona->id]
        );

        $conn->update(
            "UPDATE famiglie_persone SET stato = '0' WHERE persona_id = ? AND stato = '1'", [$persona->id]
        );
    }
}
