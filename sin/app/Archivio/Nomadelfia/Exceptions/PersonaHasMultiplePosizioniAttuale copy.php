<?php
namespace App\Nomadelfia\Exceptions;
use InvalidArgumentException;

class PersonaHasMultiplePosizioniAttuale extends InvalidArgumentException
{
    public static function named(string $nome)
    {
        return new static("La persona `{$nome}` ha più di una posizione attuale.");
    }
}
    

