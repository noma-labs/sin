<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Actions;

use Carbon\Carbon;
use App\Nomadelfia\AggiornamentoAnagrafe\Models\AggiornamentoAnagrafe;
use App\Nomadelfia\Famiglia\Models\Famiglia;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Models\Persona;

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
