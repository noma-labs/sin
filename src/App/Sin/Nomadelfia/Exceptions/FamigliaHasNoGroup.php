<?php

declare(strict_types=1);

namespace App\Nomadelfia\Exceptions;

final class FamigliaHasNoGroup extends NomadelfiaException
{
    public static function named(string $nome): self
    {
        return new self("Il capo famiglia della famiglia `{$nome}` non è assegnato/a in nessun gruppo familiare.");
    }
}
