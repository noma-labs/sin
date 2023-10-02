<?php

namespace App\Nomadelfia\Exceptions;

use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use InvalidArgumentException;

class PersonaHasMultipleGroup extends InvalidArgumentException
{
    public static function named(GruppoFamiliare $gruppo): PersonaHasMultipleGroup
    {
        return new self("Il gruppo `{$gruppo->nome}` ha più di un capogruppo.");
    }
}
