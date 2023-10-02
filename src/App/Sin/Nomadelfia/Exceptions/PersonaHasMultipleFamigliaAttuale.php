<?php

namespace App\Nomadelfia\Exceptions;

use InvalidArgumentException;

class PersonaHasMultipleFamigliaAttuale extends InvalidArgumentException
{
    public static function named(string $nome): PersonaHasMultipleFamigliaAttuale
    {
        return new self("La persona `{$nome}` ha più di uno stato familiare attuale.");
    }
}
