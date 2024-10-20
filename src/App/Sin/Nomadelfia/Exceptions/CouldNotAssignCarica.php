<?php

namespace App\Nomadelfia\Exceptions;

use Domain\Nomadelfia\Persona\Models\Persona;

class CouldNotAssignCarica extends NomadelfiaException
{
    public static function presidenteAssociazioneAlreadySet(Persona $persona): CouldNotAssignCarica
    {
        return new self("Esiste già il presidente `{$persona->nominativo}`.");
    }
}
