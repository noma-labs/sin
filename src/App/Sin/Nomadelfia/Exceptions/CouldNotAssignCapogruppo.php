<?php

namespace App\Nomadelfia\Exceptions;

use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Exception;

class CouldNotAssignCapogruppo extends Exception
{
    public static function isNotEffetivo(Persona $persona): CouldNotAssignCapogruppo
    {
        return new self("La persona `{$persona->nominativo}` non può essere capogruppo perchè non è nomadelfo effettivo.`");
    }

    public static function isNotAMan(Persona $persona): CouldNotAssignCapogruppo
    {
        return new self("La persona `{$persona->nominativo}` non può essere capogruppo perchè non è un maschi.`");
    }

    public static function GruppoHasMultipleCapogruppi(GruppoFamiliare $gruppo): CouldNotAssignCapogruppo
    {
        return new self("Il gruppo familiare `{$gruppo->nome}` non può avere più capogruppi.`");
    }
}
