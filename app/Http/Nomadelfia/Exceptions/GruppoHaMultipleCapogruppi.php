<?php

declare(strict_types=1);

namespace App\Nomadelfia\Exceptions;

final class GruppoHaMultipleCapogruppi extends NomadelfiaException
{
    public static function named(string $nome): self
    {
        return new self("Il gruppo `{$nome}` risulta avere più capogruppo.");
    }
}
