<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\UscitaFamigliaData;

class LogUscitaFamigliaAction
{
    public function execute(UscitaFamigliaData $dto): void
    {
        $dto->componenti->each(function (Persona $persona, int $key) use ($dto): Persona {
            $action = app(LogUscitaPersonaAction::class);
            $action->execute($persona, $persona->getDataEntrataNomadelfia() ?: '', $dto->data_uscita);

            return $persona;
        });
    }
}
