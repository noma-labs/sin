<?php

namespace App\Nomadelfia\Exceptions;

class PersonaHasMultipleFamigliaAttuale extends NomadelfiaException
{
    public static function named(string $nome): PersonaHasMultipleFamigliaAttuale
    {
        return new self("La persona `{$nome}` ha più di uno stato familiare attuale.");
    }
}
