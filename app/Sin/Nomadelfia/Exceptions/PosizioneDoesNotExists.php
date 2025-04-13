<?php

declare(strict_types=1);

namespace App\Nomadelfia\Exceptions;

final class PosizioneDoesNotExists extends NomadelfiaException
{
    public static function named(string $nome): self
    {
        return new self("La posizione`{$nome}` non esiste.");
    }

    public static function create(string $posizionenName): self
    {
        return new self("La posizione `{$posizionenName}` non esiste.");
    }
}
