<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Nomadelfia\Exceptions\CouldNotAssignAzienda;
use App\Nomadelfia\Exceptions\SpostaNellaFamigliaError;
use Carbon\Carbon;
use Domain\Nomadelfia\Azienda\Models\Azienda;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Support\Facades\DB;

class SpostaPersonaInUnaFamigliaAction
{
    public function execute(Persona $persona, Famiglia $famiglia, string $posizione): void
    {
        if ($famiglia->single()) {
            throw SpostaNellaFamigliaError::create($persona->nominativo, $famiglia->nome_famiglia,
                'La famiglia single non può avere più di un componente');
        }
        $attuale = $persona->famigliaAttuale();
        if (!$attuale) {
            $persona->famiglie()->attach($famiglia->id, ['stato' => '1', 'posizione_famiglia' => $posizione]);
            return;
        }
        // TODO; check se la persona può essere asseganta alla nuova famiglia
        DB::transaction(function () use (&$attuale, &$famiglia, &$posizione, $persona): void {
            $expression = DB::raw("UPDATE famiglie_persone
                SET stato = '0'
                WHERE persona_id  = :persona AND famiglia_id = :famiglia ");
            DB::connection('db_nomadelfia')->update(
                $expression->getValue(DB::connection()->getQueryGrammar()),
                [
                    'persona' => $persona->id,
                    'famiglia' => $attuale->id,
                ]
            );

            $persona->famiglie()->attach($famiglia->id, ['stato' => '1', 'posizione_famiglia' => $posizione]);
        });

    }
}
