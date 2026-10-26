<?php

declare(strict_types=1);

namespace App\Nomadelfia\Exceptions;

final class PersonaIsMaggiorenne extends NomadelfiaException
{
    public static function named(string $nome): self
    {
        return new self("La persona `{$nome}` è maggiorenne.");
    }
}
