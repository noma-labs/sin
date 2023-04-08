<?php

namespace App\Nomadelfia\Exceptions;

use Domain\Nomadelfia\Azienda\Models\Azienda;
use Domain\Nomadelfia\Persona\Models\Persona;

class CouldNotAssignAzienda extends \Exception
{
    public static function isAlreadyWorkingIntozienda(Azienda $azienda, Persona $persona): self
    {
        return new static("La persona `{$persona->nominativo}` lavora già nell'azienda {$azienda->nome_azienda}`");
    }

    public static function mansioneNotValid(string $mansione): self
    {
        return new static("La mansione  `{$mansione}` non è valida.`");
    }

    public static function isNotValidAzienda(Azienda $azienda): self
    {
        return new static("L'azienda  `{$azienda->nome_azienda}` non è valida.`");
    }
}
