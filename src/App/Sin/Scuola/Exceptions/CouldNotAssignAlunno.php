<?php

declare(strict_types=1);

namespace App\Scuola\Exceptions;

use App\Scuola\Exceptions\SchoolException;

final class CouldNotAssignAlunno extends SchoolException
{
    public static function isNotValidAnno(string $anno): self
    {
        return new self("L'anno scolastico `{$anno}` non è valido.`");
    }
}
