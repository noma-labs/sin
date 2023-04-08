<?php

namespace App\Nomadelfia\Exceptions;

use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\Persona\Models\Persona;

class CouldNotAssignMoglie extends \Exception
{
    public static function hasAlreadyMoglie(Famiglia $famiglia, Persona $persona): self
    {
        return new static("La moglie `{$persona->nominativo}` non può essere aggiunto alla famiglia `{$famiglia->nome_famiglia}` perchè esiste già una moglie.");
    }

    public static function beacuseIsMan(Famiglia $famiglia, Persona $persona): self
    {
        return new static("La moglie `{$persona->nominativo}` non può essere aggiunto alla famiglia `{$famiglia->nome_famiglia}` perchè è un maschio.");
    }

    public static function beacuseIsMinorenne(Famiglia $famiglia, Persona $persona): self
    {
        return new static("La moglie `{$persona->nominativo}` non può essere aggiunto alla famiglia `{$famiglia->nome_famiglia}` perchè minorenne.");
    }

    public static function beacuseIsSingle(Famiglia $famiglia, Persona $persona): self
    {
        return new static("La moglie `{$persona->nominativo}` non può essere aggiunto alla famiglia `{$famiglia->nome_famiglia}` perchè la famiglia è single.");
    }
}
