<?php

namespace App\Nomadelfia\Exceptions;

use Domain\Nomadelfia\Persona\Models\Persona;

class CouldNotAssignCarica extends \Exception
{
    public static function presidenteAssociazioneAlreadySet(Persona $persona): self
    {
        return new static("Esiste già il presidente `{$persona->nominativo}`.");
    }
}
