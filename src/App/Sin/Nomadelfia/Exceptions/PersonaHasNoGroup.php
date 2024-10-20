<?php

namespace App\Nomadelfia\Exceptions;

class PersonaHasNoGroup extends NomadelfiaException
{
    public static function named(string $nome): PersonaHasNoGroup
    {
        return new self("La persona `{$nome}` non è assegnato/a in nessun gruppo familiare.");
    }
}
