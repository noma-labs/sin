<?php

declare(strict_types=1);

namespace App\Nomadelfia\Exceptions;

use App\Nomadelfia\Persona\Models\Persona;

final class PersonaHasMultipleGroup extends NomadelfiaException
{
    public static function named(Persona $persona): self
    {
        return new self("La persona `{$persona->nominativo}` ha più di un gruppp familiare associato.");
    }
}
