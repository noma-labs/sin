<?php

namespace App\Nomadelfia\Exceptions;

class PersonaHasMultipleCategorieAttuale extends NomadelfiaException
{
    public static function named(string $nome): PersonaHasMultipleCategorieAttuale
    {
        return new self("La persona `{$nome}` ha più di una categoria attuale.");
    }
}
