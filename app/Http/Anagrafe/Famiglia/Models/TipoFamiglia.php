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

    /**
     * @return list<self> the tipi of a famiglia headed by a mamma
     */
    public static function mamme(): array
    {
        return [self::MammaDiVocazione, self::NomadelfaMamma, self::MammaNubile];
    }

    public function isMamma(): bool
    {
        return in_array($this, self::mamme(), true);
    }

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
