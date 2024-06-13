<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use Domain\Nomadelfia\AggiornamentoAnagrafe\Models\AggiornamentoAnagrafe;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;

class LogEntrataPersonaAction
{
    public function execute(Persona $persona, string $data_entrata, GruppoFamiliare $gruppo, ?Famiglia $famiglia)
    {
        activity(AggiornamentoAnagrafe::LOG_NAME)
            ->performedOn($persona)
            ->withProperties([
                'data_entrata' => $data_entrata,
                'gruppo' => $gruppo->nome,
                'famiglia' => ($famiglia instanceof \Domain\Nomadelfia\Famiglia\Models\Famiglia) ? $famiglia->nome_famiglia : null,
            ]
            )
            ->setEvent(AggiornamentoAnagrafe::EVENT_POPOLAZIONE_ENTER)
            ->log('Nuova entrata in Nomadelfia in data :properties.data_entrata');
    }
}
