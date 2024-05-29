<?php

namespace App\Nomadelfia\Exceptions;

use Domain\Nomadelfia\Persona\Models\Persona;
use InvalidArgumentException;

class PersonaHasMultipleGroup extends InvalidArgumentException
{
    public static function named(Persona $persona): PersonaHasMultipleGroup
    {
        return new self("La persona `{$persona->nominativo}` ha più di un gruppp familiare associato.");
    }
}
