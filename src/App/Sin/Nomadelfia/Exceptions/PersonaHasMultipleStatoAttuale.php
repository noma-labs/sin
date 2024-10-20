<?php

namespace App\Nomadelfia\Exceptions;

class PersonaHasMultipleStatoAttuale extends NomadelfiaException
{
    public static function named(string $nome): PersonaHasMultipleStatoAttuale
    {
        return new self("La persona `{$nome}` ha più di una famiglia attuale.");
    }
}
