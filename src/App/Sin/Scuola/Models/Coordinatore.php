<?php

namespace App\Scuola\Models;

use App\Traits\Enums;
use Domain\Nomadelfia\Persona\Models\Persona;

class Coordinatore extends Persona
{
    use Enums;

    protected $enumPosizione = [
        'coordinatore',
        'responsabile',
        'collaboratore',
    ];
}
