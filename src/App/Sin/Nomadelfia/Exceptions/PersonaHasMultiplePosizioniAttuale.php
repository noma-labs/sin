<?php

namespace App\Nomadelfia\Exceptions;

class PersonaHasMultiplePosizioniAttuale extends NomadelfiaException
{
    public static function named(string $nome): PersonaHasMultiplePosizioniAttuale
    {
        return new self("La persona `{$nome}` ha più di una posizione attuale.");
    }
}
