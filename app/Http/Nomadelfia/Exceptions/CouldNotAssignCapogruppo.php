<?php

declare(strict_types=1);

namespace App\Nomadelfia\Exceptions;

use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Models\Persona;

final class CouldNotAssignCapogruppo extends NomadelfiaException
{
    public static function isNotEffetivo(Persona $persona): self
    {
        return new self("La persona `{$persona->nominativo}` non può essere capogruppo perchè non è nomadelfo effettivo.`");
    }

    public static function isNotAMan(Persona $persona): self
    {
        return new self("La persona `{$persona->nominativo}` non può essere capogruppo perchè non è un maschi.`");
    }

    public static function GruppoHasMultipleCapogruppi(GruppoFamiliare $gruppo): self
    {
        return new self("Il gruppo familiare `{$gruppo->nome}` non può avere più capogruppi.`");
    }
}
