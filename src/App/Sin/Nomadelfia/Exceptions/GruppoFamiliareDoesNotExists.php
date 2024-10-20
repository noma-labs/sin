<?php

namespace App\Nomadelfia\Exceptions;

class GruppoFamiliareDoesNotExists extends NomadelfiaException
{
    public static function named(string $nome): GruppoFamiliareDoesNotExists
    {
        return new self("Il gruppo familiare `{$nome}` non esiste.");
    }
}
