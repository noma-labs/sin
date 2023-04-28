<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects;

use Domain\Nomadelfia\Persona\Models\Persona;

class UscitaPersonaData
{
    public Persona $persona;

    public string $data_uscita;

    public bool $disableFromFamily;
}
