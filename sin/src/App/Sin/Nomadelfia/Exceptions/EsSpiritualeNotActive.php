<?php
namespace App\Nomadelfia\Exceptions;

use InvalidArgumentException;

use Domain\Nomadelfia\EserciziSpirituali\Models\EserciziSpirituali;

class EsSpiritualeNotActive extends InvalidArgumentException
{
    public static function named(EserciziSpirituali $es)
    {
        return new static("Esercizi spirituale `{$es->turno}` non attivo.");
    }
}
