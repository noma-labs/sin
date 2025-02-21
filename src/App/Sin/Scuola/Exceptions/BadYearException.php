<?php

declare(strict_types=1);

namespace App\Scuola\Exceptions;

final class BadYearException extends SchoolException
{
    public static function isNotValid(string $msg): self
    {
        return new self($msg);
    }
}
