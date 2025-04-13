<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Nomadelfia\AggiornamentoAnagrafe\Models\AggiornamentoAnagrafe;
use App\Nomadelfia\Persona\Models\Persona;
use Carbon\Carbon;

final class LogUscitaPersonaAction
{
    public function execute(Persona $persona, Carbon $data_entrata, Carbon $data_uscita): void
    {
        activity(AggiornamentoAnagrafe::LOG_NAME)
            ->performedOn($persona)
            ->withProperties([
                'data_entrata' => $data_entrata->toDateString(),
                'data_uscita' => $data_uscita->toDateString(),
            ]
            )
            ->setEvent(AggiornamentoAnagrafe::EVENT_POPOLAZIONE_EXIT)
            ->log('Persona uscita in data :properties.data_uscita');

    }
}
