<?php

declare(strict_types=1);

namespace App\Nomadelfia\Exceptions;

final class SpostaNellaFamigliaError extends NomadelfiaException
{
    public static function create(string $nominativo, string $famiglia, string $msg = ''): self
    {
        return new self("Impossibile spostare {$nominativo} nella famiglia  {$famiglia}. {$msg}");
    }
}
