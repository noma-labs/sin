<?php

namespace App\Nomadelfia\Exceptions;

class PersonaErrors extends NomadelfiaException
{
    public static function named(string $nome): PersonaErrors
    {
        return new self("La persona `{$nome}` non esiste.");
    }
}
