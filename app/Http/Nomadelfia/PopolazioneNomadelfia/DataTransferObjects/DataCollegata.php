<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects;

/**
 * A row of the percorso of a person (posizione, gruppo familiare, azienda, incarico, classe) that started or ended
 * with an entrata or an uscita, and moves with it when its date is corrected.
 */
final readonly class DataCollegata
{
    public function __construct(
        public string $tipo,
        public string $nome,
        public string $inizio,
        public ?string $fine,
    ) {}
}
