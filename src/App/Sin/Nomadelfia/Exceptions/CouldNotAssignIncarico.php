<?php

declare(strict_types=1);

namespace App\Nomadelfia\Exceptions;

use Domain\Nomadelfia\Azienda\Models\Azienda;
use Domain\Nomadelfia\Incarico\Models\Incarico;
use Domain\Nomadelfia\Persona\Models\Persona;

final class CouldNotAssignIncarico extends NomadelfiaException
{
    public static function hasAlreadyIncarico(Incarico $incarico, Persona $persona): self
    {
        return new self("La persona `{$persona->nominativo}` ha già l'incarico {$incarico->nome}`");
    }

    public static function isNotValidIncarico(Azienda $incarico): self
    {
        return new self("L'incarico `{$incarico->nome_azienda}` non è valido.`");
    }

    public static function mansioneNotValid(string $mansione): self
    {
        return new self("La mansione  `{$mansione}` non è valida.`");
    }
}
