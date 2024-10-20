<?php

namespace App\Nomadelfia\Exceptions;

class PersonaIsMinorenne extends NomadelfiaException
{
    public static function named(string $nome): PersonaIsMinorenne
    {
        return new self("La persona `{$nome}` è minorenne.");
    }
}
