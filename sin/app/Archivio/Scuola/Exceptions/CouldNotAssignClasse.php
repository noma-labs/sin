<?php


namespace App\Nomadelfia\Exceptions;


use App\Nomadelfia\Models\Scuola;
use App\Nomadelfia\Models\Persona;

class CouldNotAssignClasse extends \Exception
{
    public static function isAlreadyWorkingIntozienda(Scuola $azienda, Persona $persona): self
    {
        return new static("La persona `{$persona->nominativo}` lavora già nell'azienda {$azienda->nome_azienda}`");
    }

    public static function mansioneNotValid(String $mansione): self
    {
        return new static("La mansione  `{$mansione}` non è valida.`");
    }
}