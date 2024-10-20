<?php

namespace App\Nomadelfia\Exceptions;

use Domain\Nomadelfia\EserciziSpirituali\Models\EserciziSpirituali;

class EsSpiritualeNotActive extends NomadelfiaException
{
    public static function named(EserciziSpirituali $es): EsSpiritualeNotActive
    {
        return new self("Esercizi spirituale `{$es->turno}` non attivo.");
    }
}
