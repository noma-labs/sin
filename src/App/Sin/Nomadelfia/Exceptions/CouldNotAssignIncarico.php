<?php

namespace App\Nomadelfia\Exceptions;

use Domain\Nomadelfia\Azienda\Models\Azienda;
use Domain\Nomadelfia\Persona\Models\Persona;

class CouldNotAssignIncarico extends \Exception
{
    public static function hasAlreadyIncarico(Azienda $incarico, Persona $persona): CouldNotAssignIncarico
    {
        return new self("La persona `{$persona->nominativo}` lavora già nell'azienda {$incarico->nome_azienda}`");
    }

    public static function isNotValidIncarico(Azienda $incarico): CouldNotAssignIncarico
    {
        return new self("L'incarico `{$incarico->nome_azienda}` non è valido.`");
    }

    public static function mansioneNotValid(string $mansione): CouldNotAssignIncarico
    {
        return new self("La mansione  `{$mansione}` non è valida.`");
    }
}
