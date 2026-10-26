<?php

declare(strict_types=1);

namespace App\Anagrafe\Famiglia\Models;

/**
 * The kind of famiglia: born from a marriage, or headed by one person without a coniuge (a mamma raising her figli, a celibe di elezione).
 */
enum TipoFamiglia: string
{
    case Coniugi = 'CON';
    case MammaDiVocazione = 'MAV';
    case NomadelfaMamma = 'MAM';
    case MammaNubile = 'MAN';
    case CelibeDiElezione = 'CDE';

    /**
     * @return list<self> the tipi of a famiglia headed by a mamma
     */
    public static function mamme(): array
    {
        return [self::MammaDiVocazione, self::NomadelfaMamma, self::MammaNubile];
    }

    /**
     * @return list<self> the tipi of a famiglia headed by one person without a coniuge
     */
    public static function senzaConiuge(): array
    {
        return [...self::mamme(), self::CelibeDiElezione];
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
            self::CelibeDiElezione => 'Celibe di elezione',
        };
    }
}
