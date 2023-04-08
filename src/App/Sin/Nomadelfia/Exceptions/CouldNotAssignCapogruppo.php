<?php

namespace App\Nomadelfia\Exceptions;

use Domain\Nomadelfia\Persona\Models\Persona;

class CouldNotAssignCapogruppo extends \Exception
{
    public static function isNotEffetivo(Persona $persona): self
    {
        return new static("La persona `{$persona->nominativo}` non può essere capogruppo perchè non è nomadelfo effettivo.`");
    }

    public static function isNotAMan(Persona $persona): self
    {
        return new static("La persona `{$persona->nominativo}` non può essere capogruppo perchè non è un maschi.`");
    }
}
