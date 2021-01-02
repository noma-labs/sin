<?php
namespace App\Nomadelfia\Exceptions;
use InvalidArgumentException;

class FamigliaHasNoGroup extends InvalidArgumentException
{
    public static function named(string $nome)
    {
        return new static("Il capo famiglia della famiglia `{$nome}` non è assegnato/a in nessun gruppo familiare.");
    }
}
    

