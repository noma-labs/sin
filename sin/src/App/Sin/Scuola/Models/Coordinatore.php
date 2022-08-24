<?php

namespace App\Scuola\Models;

use Domain\Nomadelfia\Persona\Models\Persona;
use App\Traits\Enums;

class Coordinatore extends Persona
{
    use Enums;

    protected $enumPosizione = [
        'coordinatore',
        'responsabile',
        'collaboratore'
    ];

}