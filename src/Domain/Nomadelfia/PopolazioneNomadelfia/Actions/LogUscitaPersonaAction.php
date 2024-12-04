<?php

declare(strict_types=1);

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use Carbon\Carbon;
use Domain\Nomadelfia\AggiornamentoAnagrafe\Models\AggiornamentoAnagrafe;
use Domain\Nomadelfia\Persona\Models\Persona;

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
