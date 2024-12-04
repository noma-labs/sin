<?php

declare(strict_types=1);

namespace App\Nomadelfia\Exceptions;

use Domain\Nomadelfia\EserciziSpirituali\Models\EserciziSpirituali;

final class EsSpiritualeNotActive extends NomadelfiaException
{
    public static function named(EserciziSpirituali $es): self
    {
        return new self("Esercizi spirituale `{$es->turno}` non attivo.");
    }
}
