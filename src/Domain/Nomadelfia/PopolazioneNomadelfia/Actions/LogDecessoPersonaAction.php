<?php

declare(strict_types=1);

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use Domain\Nomadelfia\AggiornamentoAnagrafe\Models\AggiornamentoAnagrafe;
use Domain\Nomadelfia\Persona\Models\Persona;

final class LogDecessoPersonaAction
{
    public function execute(Persona $persona, string $data_deceso): void
    {
        activity(AggiornamentoAnagrafe::LOG_NAME)
            ->performedOn($persona)
            ->withProperties([
                'data_decesso' => $data_deceso,
            ]
            )
            ->setEvent(AggiornamentoAnagrafe::EVENT_POPOLAZIONE_DEATH)
            ->log('Persona deceduta in data :properties.data_decesso');

    }
}
