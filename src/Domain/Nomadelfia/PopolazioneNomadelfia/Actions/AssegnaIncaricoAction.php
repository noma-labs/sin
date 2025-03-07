<?php

declare(strict_types=1);

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Nomadelfia\Exceptions\CouldNotAssignIncarico;
use Carbon\Carbon;
use Domain\Nomadelfia\Incarico\Models\Incarico;
use Domain\Nomadelfia\Persona\Models\Persona;

final class AssegnaIncaricoAction
{
    public function execute(Persona $persona, Incarico $incarico, Carbon $date): void
    {
        if ($persona->incarichiAttuali()->where('id', $incarico->id)->exists()) {
            throw CouldNotAssignIncarico::hasAlreadyIncarico($incarico, $persona);
        }
        $persona->incarichi()->attach($incarico->id, [
            'data_inizio' => $date->toDateString(),
        ]);
    }
}
