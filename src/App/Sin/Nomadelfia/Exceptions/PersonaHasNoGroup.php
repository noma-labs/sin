<?php

declare(strict_types=1);

namespace App\Nomadelfia\Exceptions;

final class PersonaHasNoGroup extends NomadelfiaException
{
    public static function named(string $nome): self
    {
        return new self("La persona `{$nome}` non è assegnato/a in nessun gruppo familiare.");
    }
}
