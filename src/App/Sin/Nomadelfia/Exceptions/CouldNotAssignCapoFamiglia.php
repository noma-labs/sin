<?php

declare(strict_types=1);

namespace App\Nomadelfia\Exceptions;

use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\Persona\Models\Persona;

final class CouldNotAssignCapoFamiglia extends NomadelfiaException
{
    public static function hasAlreadyCapoFamiglia(Famiglia $famiglia, Persona $capofamiglia): self
    {
        return new self("Il capo famiglia `{$capofamiglia->nominativo}` non può essere aggiunto alla famiglia `{$famiglia->nome_famiglia}` perchè esiste già un capo famiglia.");
    }

    public static function beacuseIsMinorenne(Famiglia $famiglia, Persona $persona): self
    {
        return new self("Il capo famiglia {$persona->nominativo}` non può essere aggiunto alla famiglia `{$famiglia->nome_famiglia}` perchè minorenne.");
    }

    public static function beacuseIsSingle(Famiglia $famiglia, Persona $persona): self
    {
        return new self("Il capo famiglia {$persona->nominativo}` non può essere aggiunto alla famiglia `{$famiglia->nome_famiglia}` perchè la famiglia è single.");
    }
}
