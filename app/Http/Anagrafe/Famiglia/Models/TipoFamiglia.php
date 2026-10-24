<?php

declare(strict_types=1);

namespace App\Anagrafe\Famiglia\Models;

/**
 * The kind of famiglia: born from a marriage, or headed by a mamma raising her figli without a husband.
 */
enum TipoFamiglia: string
{
    case Coniugi = 'CON';
    case MammaDiVocazione = 'MAV';
    case NomadelfaMamma = 'MAM';
    case MammaNubile = 'MAN';

    public function nome(): string
    {
        return match ($this) {
            self::Coniugi => 'Coniugi',
            self::MammaDiVocazione => 'Mamma di vocazione',
            self::NomadelfaMamma => 'Nomadelfa mamma',
            self::MammaNubile => 'Mamma nubile',
        };
    }
}
