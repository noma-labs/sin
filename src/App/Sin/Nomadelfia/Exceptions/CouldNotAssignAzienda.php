<?php

declare(strict_types=1);

namespace App\Nomadelfia\Exceptions;

use Domain\Nomadelfia\Azienda\Models\Azienda;
use Domain\Nomadelfia\Persona\Models\Persona;

final class CouldNotAssignAzienda extends NomadelfiaException
{
    public static function isAlreadyWorkingIntozienda(Azienda $azienda, Persona $persona): self
    {
        return new self("La persona `{$persona->nominativo}` lavora già nell'azienda {$azienda->nome_azienda}`");
    }

    public static function mansioneNotValid(string $mansione): self
    {
        return new self("La mansione  `{$mansione}` non è valida.`");
    }

    public static function isNotValidAzienda(Azienda $azienda): self
    {
        return new self("L'azienda  `{$azienda->nome_azienda}` non è valida.`");
    }
}
