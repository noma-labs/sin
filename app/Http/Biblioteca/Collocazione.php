<?php

declare(strict_types=1);

namespace App\Biblioteca;

/**
 * Collocazione fisica di un libro nella forma XYZnnn: X parete, Y colonna, Z ripiano, nnn posizione sul ripiano.
 * Le collocazioni SP (elaborati della Scuola Parentale) hanno la forma SP + colonna + numero, senza ripiano.
 */
final readonly class Collocazione
{
    private function __construct(
        public string $colonna,
        public ?string $ripiano,
        public ?int $numero,
    ) {}

    /**
     * Legge una collocazione completa (es. "DGB039") o le sole lettere (es. "DGB"); restituisce null se non è valida.
     */
    public static function parse(?string $collocazione): ?self
    {
        $collocazione = mb_strtoupper(mb_trim((string) $collocazione));
        if (preg_match('/^([A-Z]{3})(\d{1,3})?$/', $collocazione, $parti) !== 1) {
            return null;
        }

        $lettere = $parti[1];
        $numero = isset($parti[2]) ? (int) $parti[2] : null;

        if (str_starts_with($lettere, 'SP')) {
            return new self($lettere, null, $numero);
        }

        return new self(mb_substr($lettere, 0, 2), mb_substr($lettere, 2, 1), $numero);
    }

    public function isElaborato(): bool
    {
        return $this->ripiano === null;
    }
}
