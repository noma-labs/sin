<?php

namespace App\Nomadelfia\Exceptions;

use InvalidArgumentException;

class PosizioneDoesNotExists extends InvalidArgumentException
{
    public static function named(string $nome)
    {
        return new static("La posizione`{$nome}` non esiste.");
    }

    public static function create(string $posizionenName)
    {
        return new static("La posizione `{$posizionenName}` non esiste.");
    }
}
