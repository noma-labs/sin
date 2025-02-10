<?php

declare(strict_types=1);

namespace App\Nomadelfia\Exceptions;

use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\Persona\Models\Persona;

final class CouldNotAssignMoglie extends NomadelfiaException
{
    public static function hasAlreadyMoglie(Famiglia $famiglia, Persona $persona): self
    {
        return new self("La moglie `{$persona->nominativo}` non può essere aggiunto alla famiglia `{$famiglia->nome_famiglia}` perchè esiste già una moglie.");
    }

    public static function beacuseIsMan(Famiglia $famiglia, Persona $persona): self
    {
        return new self("La moglie `{$persona->nominativo}` non può essere aggiunto alla famiglia `{$famiglia->nome_famiglia}` perchè è un maschio.");
    }

    public static function beacuseIsMinorenne(Famiglia $famiglia, Persona $persona): self
    {
        return new self("La moglie `{$persona->nominativo}` non può essere aggiunto alla famiglia `{$famiglia->nome_famiglia}` perchè minorenne.");
    }

    public static function beacuseIsSingle(Famiglia $famiglia, Persona $persona): self
    {
        return new self("La moglie `{$persona->nominativo}` non può essere aggiunto alla famiglia `{$famiglia->nome_famiglia}` perchè la famiglia è single.");
    }
}
