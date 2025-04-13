<?php

declare(strict_types=1);

namespace App\Nomadelfia\Exceptions;

final class StatoDoesNotExists extends NomadelfiaException
{
    public static function named(string $nome): self
    {
        return new self("Lo stato `{$nome}` non esiste.");
    }

    public static function create(string $name): self
    {
        return new self("Lo stato `{$name}` non esiste.");
    }
}
