<?php

declare(strict_types=1);

namespace App\Anagrafe\Famiglia\Models;

/**
 * The kind of famiglia: it says whether a capofamiglia without coniuge in the famiglia is married (the coniuge lives outside Nomadelfia)
 * or an unmarried mamma raising her figli.
 */
enum TipoFamiglia: string
{
    case Coniugi = 'CONIUGI';
    case MammaDiVocazione = 'MAMMA DI VOCAZIONE';
    case MammaNubile = 'MAMMA NUBILE';

    public function nome(): string
    {
        return match ($this) {
            self::Coniugi => 'Coniugi',
            self::MammaDiVocazione => 'Mamma di vocazione',
            self::MammaNubile => 'Mamma nubile',
        };
    }

    public function isMamma(): bool
    {
        return $this !== self::Coniugi;
    }
}
