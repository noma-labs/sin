<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects;

use App\Nomadelfia\Persona\Models\Persona;
use Carbon\Carbon;

final class UscitaPersonaData
{
    public Persona $persona;

    public Carbon $data_uscita;

    public bool $disableFromFamily;
}
