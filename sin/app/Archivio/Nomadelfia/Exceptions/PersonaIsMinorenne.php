<?php
namespace App\Nomadelfia\Exceptions;

use InvalidArgumentException;

class PersonaIsMinorenne extends InvalidArgumentException
{
    public static function named(string $nome)
    {
        return new static("La persona `{$nome}` è minorenne.");
    }
}
