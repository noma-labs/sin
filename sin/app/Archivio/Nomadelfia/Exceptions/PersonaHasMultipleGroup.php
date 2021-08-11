<?php
namespace App\Nomadelfia\Exceptions;
use App\Nomadelfia\Models\GruppoFamiliare;
use InvalidArgumentException;

class GruppoHaMultipleCapogruppi extends InvalidArgumentException
{
    public static function named(GruppoFamiliare $gruppo)
    {
        return new static("Il gruppo `{$gruppo->nome}` ha più di un capogruppo.");
    }
}
    

