<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Nomadelfia\Exceptions\CouldNotAssignAzienda;
use Carbon\Carbon;
use App\Nomadelfia\Azienda\Models\Azienda;
use App\Nomadelfia\Persona\Models\Persona;

final class AssegnaAziendaAction
{
    public function execute(Persona $persona, Azienda $azienda, Carbon $date, string $mansione): void
    {
        if (strcasecmp($mansione, 'LAVORATORE') !== 0 && strcasecmp($mansione, 'RESPONSABILE AZIENDA') !== 0) {
            throw CouldNotAssignAzienda::mansioneNotValid($mansione);
        }
        if (! $azienda->isAzienda()) {
            throw CouldNotAssignAzienda::isNotValidAzienda($azienda);
        }
        if ($persona->aziendeAttuali->contains($azienda->id)) {
            throw CouldNotAssignAzienda::isAlreadyWorkingIntozienda($azienda, $persona);
        }
        $persona->aziende()->attach($azienda->id, [
            'stato' => 'Attivo',
            'data_inizio_azienda' => $date->toDateString(),
            'mansione' => $mansione,
        ]);
    }
}
