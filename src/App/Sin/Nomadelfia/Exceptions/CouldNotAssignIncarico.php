<?php

namespace App\Nomadelfia\Exceptions;

use Domain\Nomadelfia\Azienda\Models\Azienda;
use Domain\Nomadelfia\Incarico\Models\Incarico;
use Domain\Nomadelfia\Persona\Models\Persona;

class CouldNotAssignIncarico extends NomadelfiaException
{
    public static function hasAlreadyIncarico(Incarico $incarico, Persona $persona): CouldNotAssignIncarico
    {
        return new self("La persona `{$persona->nominativo}` ha già l'incarico {$incarico->nome}`");
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
