<?php

namespace App\Nomadelfia\Exceptions;

use InvalidArgumentException;

class PersonaIsMinorenne extends InvalidArgumentException
{
    public static function named(string $nome): PersonaIsMinorenne
    {
        return new self("La persona `{$nome}` è minorenne.");
    }
}
