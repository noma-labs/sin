<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use Domain\Nomadelfia\AggiornamentoAnagrafe\Models\AggiornamentoAnagrafe;
use Domain\Nomadelfia\Persona\Models\Persona;

class LogUscitaPersonaAction
{
    public function execute(Persona $persona, string $data_entrata, string $data_uscita)
    {
        activity(AggiornamentoAnagrafe::LOG_NAME)
            ->performedOn($persona)
            ->withProperties([
                    'data_entrata' => $data_entrata,
                    'data_uscita' => $data_uscita,
                ]
            )
            ->setEvent(AggiornamentoAnagrafe::EVENT_POPOLAZIONE_EXIT)
            ->log('Persona uscita in data :properties.data_uscita');

    }
}
