<?php

declare(strict_types=1);

namespace App\Scuola\Exceptions;

final class GeneralException extends SchoolException
{
    public static function withMsg(string $msg): self
    {
        return new self($msg);
    }
}
