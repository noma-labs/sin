<?php

namespace App\Nomadelfia\Exceptions;

use InvalidArgumentException;

class GruppoFamiliareDoesNotExists extends InvalidArgumentException
{
    public static function named(string $nome): GruppoFamiliareDoesNotExists
    {
        return new self("Il gruppo familiare `{$nome}` non esiste.");
    }
}
