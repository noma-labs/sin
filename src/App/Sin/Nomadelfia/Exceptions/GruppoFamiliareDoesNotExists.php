<?php

namespace App\Nomadelfia\Exceptions;

use InvalidArgumentException;

class GruppoFamiliareDoesNotExists extends InvalidArgumentException
{
    public static function named(string $nome)
    {
        return new static("Il gruppo familiare `{$nome}` non esiste.");
    }
}
