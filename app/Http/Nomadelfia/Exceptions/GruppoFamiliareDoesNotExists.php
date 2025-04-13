<?php

declare(strict_types=1);

namespace App\Nomadelfia\Exceptions;

final class GruppoFamiliareDoesNotExists extends NomadelfiaException
{
    public static function named(string $nome): self
    {
        return new self("Il gruppo familiare `{$nome}` non esiste.");
    }
}
