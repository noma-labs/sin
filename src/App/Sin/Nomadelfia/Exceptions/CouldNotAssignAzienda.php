<?php

namespace App\Nomadelfia\Exceptions;

use Domain\Nomadelfia\Azienda\Models\Azienda;
use Domain\Nomadelfia\Persona\Models\Persona;
use Exception;

class CouldNotAssignAzienda extends Exception
{
    public static function isAlreadyWorkingIntozienda(Azienda $azienda, Persona $persona): CouldNotAssignAzienda
    {
        return new self("La persona `{$persona->nominativo}` lavora già nell'azienda {$azienda->nome_azienda}`");
    }

    public static function mansioneNotValid(string $mansione): CouldNotAssignAzienda
    {
        return new self("La mansione  `{$mansione}` non è valida.`");
    }

    public static function isNotValidAzienda(Azienda $azienda): CouldNotAssignAzienda
    {
        return new self("L'azienda  `{$azienda->nome_azienda}` non è valida.`");
    }
}
