<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects;

/**
 * The kind of anniversary remembered by the community, with the event it counts the years from.
 */
enum TipoRicorrenza: string
{
    case Matrimonio = 'matrimonio';
    case Entrata = 'entrata';
    case MammaDiVocazione = 'mamma';
    case Sacerdozio = 'sacerdozio';
    case Decesso = 'decesso';

    public function nome(): string
    {
        return match ($this) {
            self::Matrimonio => 'Matrimonio',
            self::Entrata => 'Entrata in Nomadelfia',
            self::MammaDiVocazione => 'Mamma di vocazione',
            self::Sacerdozio => 'Sacerdozio',
            self::Decesso => 'Anniversario morte',
        };
    }
}
