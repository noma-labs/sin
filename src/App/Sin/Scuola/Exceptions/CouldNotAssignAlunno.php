<?php

namespace App\Scuola\Exceptions;

use Exception;

class CouldNotAssignAlunno extends Exception
{
    public static function isNotValidAnno(string $anno): self
    {
        return new static("L'anno scolastico `{$anno}` non è valido.`");
    }
}
