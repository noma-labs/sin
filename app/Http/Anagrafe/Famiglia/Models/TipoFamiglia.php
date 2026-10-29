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

    /**
     * @return list<self> the tipi of a famiglia that a person of the given sesso can start: as coniuge, or as the one heading it
     */
    public static function perSesso(string $sesso): array
    {
        return array_values(array_filter(
            self::cases(),
            fn (self $tipo): bool => $tipo === self::Coniugi || $tipo->sessoCapofamiglia() === $sesso,
        ));
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

    public function descrizione(): string
    {
        return match ($this) {
            self::Coniugi => 'Nata da un matrimonio',
            self::MammaDiVocazione => 'Una mamma che accoglie figli per vocazione',
            self::NomadelfaMamma => 'Una Nomadelfa che diventa mamma',
            self::MammaNubile => 'Una mamma con i propri figli',
            self::CelibeDiElezione => 'Un uomo che sceglie il celibato e guida una famiglia',
        };
    }

    /**
     * The meaning of the data_creazione of the famiglia: the matrimonio for the coniugi, the start of the famiglia otherwise.
     */
    public function etichettaDataCreazione(): string
    {
        return match ($this) {
            self::Coniugi => 'Data matrimonio',
            self::MammaDiVocazione => 'Data inizio vocazione',
            self::NomadelfaMamma => 'Data inizio come Nomadelfa mamma',
            self::MammaNubile => 'Data inizio famiglia',
            self::CelibeDiElezione => 'Data della scelta',
        };
    }

    /**
     * The sesso of the capofamiglia: the marito for the coniugi, the mamma for the famiglie of a mamma.
     */
    public function sessoCapofamiglia(): string
    {
        return $this->isMamma() ? 'F' : 'M';
    }
}
