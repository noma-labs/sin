<?php
namespace App\Nomadelfia\Exceptions;
use InvalidArgumentException;

class StatoDoesNotExists extends InvalidArgumentException
{
    public static function named(string $nome)
    {
        return new static("Lo stato `{$nome}` non esiste.");
    }

    public static function create(string $name)
    {
        return new static("o stato `{$name}` non esiste.");
    }
    // public static function withId(int $roleId)
    // {
    //     return new static("There is no role with id `{$roleId}`.");
    // }
}