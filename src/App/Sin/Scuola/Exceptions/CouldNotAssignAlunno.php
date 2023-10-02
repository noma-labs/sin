<?php

namespace App\Scuola\Exceptions;

class CouldNotAssignAlunno extends \Exception
{
    public static function isNotValidAnno(string $anno): self
    {
        return new self("L'anno scolastico `{$anno}` non è valido.`");
    }
}
