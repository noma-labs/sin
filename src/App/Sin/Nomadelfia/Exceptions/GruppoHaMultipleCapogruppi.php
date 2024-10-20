<?php

namespace App\Nomadelfia\Exceptions;

class GruppoHaMultipleCapogruppi extends NomadelfiaException
{
    public static function named(string $nome): GruppoHaMultipleCapogruppi
    {
        return new self("La persona `{$nome}` risulta essere in più di un gruppo familiare.");
    }
}
