<?php


namespace App\Scuola\Exceptions;


use App\Nomadelfia\Models\AnnoScolastico;

class CouldNotAssignAlunno extends \Exception
{

    public static function isNotValidAnno(String $anno): self
    {
        return new static("L'anno scolastico `{$anno}` non è valido.`");
    }
}