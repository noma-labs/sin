<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\UscitaFamigliaData;

final class LogUscitaFamigliaAction
{
    public function execute(UscitaFamigliaData $dto): void
    {
        $dto->componenti->each(function (Persona $persona, int $key) use ($dto): Persona {
            $action = app(LogUscitaPersonaAction::class);
            $action->execute($persona, $persona->getDataEntrataNomadelfia(), $dto->data_uscita);

            return $persona;
        });
    }
}
