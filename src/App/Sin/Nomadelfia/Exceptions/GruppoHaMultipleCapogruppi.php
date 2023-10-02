<?php

namespace App\Nomadelfia\Exceptions;

use InvalidArgumentException;

class GruppoHaMultipleCapogruppi extends InvalidArgumentException
{
    public static function named(string $nome): GruppoHaMultipleCapogruppi
    {
        return new self("La persona `{$nome}` risulta essere in più di un gruppo familiare.");
    }
}
