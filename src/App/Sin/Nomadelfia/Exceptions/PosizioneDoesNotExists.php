<?php

namespace App\Nomadelfia\Exceptions;

use InvalidArgumentException;

class PosizioneDoesNotExists extends InvalidArgumentException
{
    public static function named(string $nome): PosizioneDoesNotExists
    {
        return new self("La posizione`{$nome}` non esiste.");
    }

    public static function create(string $posizionenName): PosizioneDoesNotExists
    {
        return new self("La posizione `{$posizionenName}` non esiste.");
    }
}
