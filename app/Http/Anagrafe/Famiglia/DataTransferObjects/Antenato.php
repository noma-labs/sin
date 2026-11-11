<?php

declare(strict_types=1);

namespace App\Anagrafe\Famiglia\DataTransferObjects;

use App\Anagrafe\Persona\Models\Persona;
use Illuminate\Support\Collection;

/**
 * A genitore in the albero familiare with his own genitori, the nonni of the persona.
 * The genitori are null when the genitore has no famiglia d'origine registered.
 */
final readonly class Antenato
{
    /**
     * @param  Collection<int, Persona>|null  $genitori  the capofamiglia first
     */
    public function __construct(
        public Persona $persona,
        public ?Collection $genitori,
    ) {}
}
