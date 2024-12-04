<?php

declare(strict_types=1);

namespace App\Scuola\Models;

use App\Traits\Enums;
use Domain\Nomadelfia\Persona\Models\Persona;

final class Coordinatore extends Persona
{
    use Enums;

    protected $enumPosizione = [
        'coordinatore',
        'responsabile',
        'collaboratore',
    ];
}
