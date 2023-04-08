<?php

namespace App\Nomadelfia\Exceptions;

use Domain\Nomadelfia\EserciziSpirituali\Models\EserciziSpirituali;
use InvalidArgumentException;

class EsSpiritualeNotActive extends InvalidArgumentException
{
    public static function named(EserciziSpirituali $es)
    {
        return new static("Esercizi spirituale `{$es->turno}` non attivo.");
    }
}
