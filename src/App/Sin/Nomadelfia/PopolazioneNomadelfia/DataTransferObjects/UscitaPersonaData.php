<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects;

use Carbon\Carbon;
use App\Nomadelfia\Persona\Models\Persona;

final class UscitaPersonaData
{
    public Persona $persona;

    public Carbon $data_uscita;

    public bool $disableFromFamily;
}
