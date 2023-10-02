<?php

namespace App\Nomadelfia\Exceptions;

use InvalidArgumentException;

class PersonaHasMultiplePosizioniAttuale extends InvalidArgumentException
{
    public static function named(string $nome): PersonaHasMultiplePosizioniAttuale
    {
        return new self("La persona `{$nome}` ha più di una posizione attuale.");
    }
}
