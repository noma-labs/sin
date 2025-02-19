<?php

declare(strict_types=1);

namespace App\Scuola\Exceptions;

use App\Scuola\Exceptions\SchoolException;

final class BadDimensionException extends SchoolException
{
    public static function isNotValid(string $dimension, string $msg): self
    {
        return new self("La dimesione `{$dimension}` non è valida. ".$msg);
    }
}
