<?php

namespace App\Nomadelfia\Exceptions;

use InvalidArgumentException;

class PersonaHasMultipleCategorieAttuale extends InvalidArgumentException
{
    public static function named(string $nome): PersonaHasMultipleCategorieAttuale
    {
        return new self("La persona `{$nome}` ha più di una categoria attuale.");
    }
}
