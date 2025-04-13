<?php

declare(strict_types=1);

namespace App\Scuola\Models;

use App\Nomadelfia\Persona\Models\Persona;
use App\Traits\Enums;

final class Coordinatore extends Persona
{
    use Enums;

    protected $enumPosizione = [
        'coordinatore',
        'responsabile',
        'collaboratore',
    ];
}
