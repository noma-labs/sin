<?php

namespace App\Nomadelfia\Exceptions;

use InvalidArgumentException;

class PersonaErrors extends InvalidArgumentException
{
    public static function named(string $nome): PersonaErrors
    {
        return new self("La persona `{$nome}` non esiste.");
    }
}
