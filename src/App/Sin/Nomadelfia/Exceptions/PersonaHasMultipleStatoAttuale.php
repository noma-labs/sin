<?php

declare(strict_types=1);

namespace App\Nomadelfia\Exceptions;

final class PersonaHasMultipleStatoAttuale extends NomadelfiaException
{
    public static function named(string $nome): self
    {
        return new self("La persona `{$nome}` ha più di una famiglia attuale.");
    }
}
