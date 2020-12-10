<?php
namespace App\Nomadelfia\Exceptions;
use InvalidArgumentException;

class PersonaHasNoGroup extends InvalidArgumentException
{
    public static function named(string $nome)
    {
        return new static("La persona `{$nome}` non è assegnato/a in nessun gruppo familiare.");
    }
}
    

