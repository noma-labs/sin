<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Actions;

use Carbon\Carbon;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Models\Persona;
use Illuminate\Support\Facades\DB;

final class AssegnaGruppoFamiliareAction
{
    public function execute(Persona $persona, GruppoFamiliare $gruppo, Carbon $date): void
    {

        DB::transaction(function () use ($persona, $gruppo, $date): void {
            $attuale = $persona->gruppofamiliareAttuale();
            if ($attuale) {
                $persona->gruppifamiliari()->updateExistingPivot($attuale->id, [
                    'stato' => '0',
                    'data_uscita_gruppo' => $date->toDatestring(),
                ]);
            }
            $persona->gruppifamiliari()->attach($gruppo->id, ['stato' => '1', 'data_entrata_gruppo' => $date->toDatestring()]);
        });
    }
}
