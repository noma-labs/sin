<?php

namespace App\Nomadelfia\Exceptions;

use InvalidArgumentException;

class PersonaErrors extends InvalidArgumentException
{
    public static function named(string $nome)
    {
        return new static("La persona `{$nome}` non esiste.");
    }
}
