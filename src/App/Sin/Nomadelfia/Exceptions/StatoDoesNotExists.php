<?php

namespace App\Nomadelfia\Exceptions;

use InvalidArgumentException;

class StatoDoesNotExists extends InvalidArgumentException
{
    public static function named(string $nome): StatoDoesNotExists
    {
        return new self("Lo stato `{$nome}` non esiste.");
    }

    public static function create(string $name): StatoDoesNotExists
    {
        return new self("Lo stato `{$name}` non esiste.");
    }
}
