<?php

namespace App\Nomadelfia\Exceptions;

use Domain\Nomadelfia\Azienda\Models\Azienda;
use Domain\Nomadelfia\Persona\Models\Persona;

class CouldNotAssignIncarico extends \Exception
{
    public static function hasAlreadyIncarico(Azienda $incarico, Persona $persona): self
    {
        return new static("La persona `{$persona->nominativo}` lavora già nell'azienda {$incarico->nome_azienda}`");
    }

    public static function isNotValidIncarico(Azienda $incarico): self
    {
        return new static("L'incarico `{$incarico->nome_azienda}` non è valido.`");
    }

    public static function mansioneNotValid(string $mansione): self
    {
        return new static("La mansione  `{$mansione}` non è valida.`");
    }
}
