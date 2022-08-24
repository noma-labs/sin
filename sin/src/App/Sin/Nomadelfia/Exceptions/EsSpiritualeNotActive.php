<?php
namespace App\Nomadelfia\Exceptions;

use InvalidArgumentException;

use App\Nomadelfia\Models\EserciziSpirituali;

class EsSpiritualeNotActive extends InvalidArgumentException
{
    public static function named(EserciziSpirituali $es)
    {
        return new static("Esercizi spirituale `{$es->turno}` non attivo.");
    }
}
