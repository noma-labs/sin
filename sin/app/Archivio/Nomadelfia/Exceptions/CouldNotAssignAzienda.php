<?php


namespace App\Nomadelfia\Exceptions;


use App\Nomadelfia\Models\Azienda;
use App\Nomadelfia\Models\Persona;

class CouldNotAssignAzienda extends \Exception
{
    public static function isAlreadyWorkingIntozienda(Azienda $azienda, Persona $persona): self
    {
        return new static("La persona `{$persona->nominativo}` lavora già nell'azienda {$azienda->nome_azienda}`");
    }

    public static function mansioneNotValid(String $mansione): self
    {
        return new static("La mansione  `{$mansione}` non è valida.`");
    }
}