<?php

declare(strict_types=1);

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use Carbon\Carbon;
use Domain\Nomadelfia\AggiornamentoAnagrafe\Models\AggiornamentoAnagrafe;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;

final class LogEntrataPersonaAction
{
    public function execute(Persona $persona, Carbon $data_entrata, GruppoFamiliare $gruppo, ?Famiglia $famiglia): void
    {
        activity(AggiornamentoAnagrafe::LOG_NAME)
            ->performedOn($persona)
            ->withProperties([
                'data_entrata' => $data_entrata->toDateString(),
                'gruppo' => $gruppo->nome,
                'famiglia' => ($famiglia) ? $famiglia->nome_famiglia : null,
            ]
            )
            ->setEvent(AggiornamentoAnagrafe::EVENT_POPOLAZIONE_ENTER)
            ->log('Nuova entrata in Nomadelfia in data :properties.data_entrata');
    }
}
