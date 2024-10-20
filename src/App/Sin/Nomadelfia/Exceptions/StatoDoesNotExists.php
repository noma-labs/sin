<?php

namespace App\Nomadelfia\Exceptions;

class StatoDoesNotExists extends NomadelfiaException
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
