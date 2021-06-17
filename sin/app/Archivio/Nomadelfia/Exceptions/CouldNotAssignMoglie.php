<?php


namespace App\Nomadelfia\Exceptions;


use App\Nomadelfia\Models\Famiglia;
use App\Nomadelfia\Models\Persona;

class CouldNotAssignMoglie extends \Exception
{
    public static function hasAlreadyMoglie(Famiglia $famiglia, Persona $persona): self
    {
        return new static("La moglie {$persona->nominativo}` non può essere aggiunto alla famiglia `{$famiglia->nome_famiglia}` perchè esiste già una moglie.");
    }

    public static function beacuseIsMan(Famiglia $famiglia, Persona $persona): self
    {
        return new static("La persona `{$persona->nominativo}` non può essere aggiunto alla famiglia `{$famiglia->nome_famiglia}` perchè è un maschio.");
    }

    public static function beacuseIsMinorenne(Famiglia $famiglia, Persona $persona): self
    {
        return new static("La persona {$persona->nominativo}` non può essere aggiunto alla famiglia `{$famiglia->nome_famiglia}` come moglie perchè minorenne.");
    }

    public static function beacuseIsSingle(Famiglia $famiglia, Persona $persona): self
    {
        return new static("La moglie {$persona}` non può essere aggiunto alla famiglia `{$famiglia->nome_famiglia}` perchè la famiglia è single.");
    }

}