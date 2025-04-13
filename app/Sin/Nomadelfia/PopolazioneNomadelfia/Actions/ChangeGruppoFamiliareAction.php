<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Actions;

use Carbon\Carbon;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Models\Persona;
use Illuminate\Support\Facades\DB;

final class ChangeGruppoFamiliareAction
{
    public function execute(Persona $persona, GruppoFamiliare $gruppo_from, Carbon $date_start_current, Carbon $date_end_current, GruppoFamiliare $gruppo_to, Carbon $date_start_new): void
    {

        DB::transaction(function () use ($persona, $gruppo_from, $gruppo_to, $date_start_current, $date_end_current, $date_start_new): void {
            // disabilita il gruppo attuale
            $expression = DB::raw("UPDATE gruppi_persone
                     SET gruppi_persone.stato = '0', data_uscita_gruppo = :dataout
                     WHERE persona_id = :p  AND gruppo_famigliare_id = :g AND data_entrata_gruppo = :datain
                    ");
            DB::connection('db_nomadelfia')->update(
                $expression->getValue(DB::connection()->getQueryGrammar()),
                [
                    'p' => $persona->id,
                    'g' => $gruppo_from->id,
                    'datain' => $date_start_current->toDateString(),
                    'dataout' => $date_end_current->toDateString(),
                ]
            );
            // disabilita tutti i gruppi familiare della persona
            $expression = DB::raw("UPDATE gruppi_persone
                    SET gruppi_persone.stato = '0'
                    WHERE persona_id = :p
                    ");
            DB::connection('db_nomadelfia')->update(
                $expression->getValue(DB::connection()->getQueryGrammar()),
                ['p' => $persona->id]
            );

            // assegna il nuovo gruppo alla persona
            $expression = DB::raw("INSERT INTO gruppi_persone (persona_id, gruppo_famigliare_id, stato, data_entrata_gruppo)
                    VALUES (:persona, :gruppo, '1', :datain) ");
            DB::connection('db_nomadelfia')->insert(
                $expression->getValue(DB::connection()->getQueryGrammar()),
                ['persona' => $persona->id, 'gruppo' => $gruppo_to->id, 'datain' => $date_start_new->toDateString()]
            );
        });
    }
}
