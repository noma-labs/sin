<?php

declare(strict_types=1);

namespace App\Nomadelfia\Exceptions;

use Domain\Nomadelfia\Persona\Models\Persona;

final class CouldNotAssignCarica extends NomadelfiaException
{
    public static function presidenteAssociazioneAlreadySet(Persona $persona): self
    {
        return new self("Esiste già il presidente `{$persona->nominativo}`.");
    }
}
