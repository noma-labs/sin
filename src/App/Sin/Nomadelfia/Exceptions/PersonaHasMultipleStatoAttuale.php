<?php

namespace App\Nomadelfia\Exceptions;

use InvalidArgumentException;

class PersonaHasMultipleStatoAttuale extends InvalidArgumentException
{
    public static function named(string $nome): PersonaHasMultipleStatoAttuale
    {
        return new self("La persona `{$nome}` ha più di una famiglia attuale.");
    }
}
