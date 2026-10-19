<?php

declare(strict_types=1);

namespace App\Anagrafe\Exceptions;

final class PersonaHasMultipleFamigliaAttuale extends AnagrafeException
{
    public static function named(string $nome): self
    {
        return new self("La persona `{$nome}` ha più di uno stato familiare attuale.");
    }
}
