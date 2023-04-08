<?php

namespace App\Nomadelfia\Exceptions;

use InvalidArgumentException;

class PersonaHasMultipleFamigliaAttuale extends InvalidArgumentException
{
    public static function named(string $nome)
    {
        return new static("La persona `{$nome}` ha più di uno stato familiare attuale.");
    }
}
