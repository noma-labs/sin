<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\UscitaFamigliaData;

class LogUscitaFamigliaAction
{
    public function execute(UscitaFamigliaData $dto)
    {
        $dto->componenti->each(function ($persona, $key) use ($dto) {
            $action = app(LogUscitaPersonaAction::class);
            $action->execute($persona, $persona->getDataEntrataNomadelfia() ?: '', $dto->data_uscita);
        });
    }
}
