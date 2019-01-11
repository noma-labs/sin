<?php
namespace App\Nomadelfia\Exceptions;
use InvalidArgumentException;

class GruppoFamiliareDoesNOtExists extends InvalidArgumentException
{
    public static function named(string $nome)
    {
        return new static("Il gruppo familiare `{$nome}` non esiste.");
    }
    // public static function withId(int $roleId)
    // {
    //     return new static("There is no role with id `{$roleId}`.");
    // }
}