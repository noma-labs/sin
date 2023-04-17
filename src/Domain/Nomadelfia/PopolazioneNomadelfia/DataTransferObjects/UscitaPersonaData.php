<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects;

use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;

class UscitaPersonaData
{
    public Persona $persona;

    public string $data_uscita;

    public string $data_entrata;

    public bool $disableFromFamily;

}
