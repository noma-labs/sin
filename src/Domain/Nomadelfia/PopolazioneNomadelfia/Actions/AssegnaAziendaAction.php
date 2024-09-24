<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use App\Nomadelfia\Exceptions\CouldNotAssignAzienda;
use Carbon\Carbon;
use Domain\Nomadelfia\Azienda\Models\Azienda;
use Domain\Nomadelfia\Persona\Models\Persona;

class AssegnaAziendaAction
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
