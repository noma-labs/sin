<?php

declare(strict_types=1);

namespace App\Scuola\Models;

/**
 * The kind of work an elaborato is: a lettera, a tema, a disegno, a compito in classe, a poesia, a fascicolo personale, a ricerca or a diario.
 */
enum TipoElaborato: string
{
    case Lettera = 'lettera';
    case Tema = 'tema';
    case Disegno = 'disegno';
    case CompitoInClasse = 'compito';
    case Poesia = 'poesia';
    case FascicoloPersonale = 'fascicolo';
    case Ricerca = 'ricerca';
    case Diario = 'diario';

    public function nome(): string
    {
        return match ($this) {
            self::Lettera => 'Lettera',
            self::Tema => 'Tema',
            self::Disegno => 'Disegno',
            self::CompitoInClasse => 'Compito in classe',
            self::Poesia => 'Poesia',
            self::FascicoloPersonale => 'Fascicolo personale',
            self::Ricerca => 'Ricerca',
            self::Diario => 'Diario',
        };
    }
}
