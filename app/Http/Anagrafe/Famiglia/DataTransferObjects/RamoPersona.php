<?php

declare(strict_types=1);

namespace App\Anagrafe\Famiglia\DataTransferObjects;

use App\Anagrafe\Persona\Models\Persona;

/**
 * A persona in the albero familiare with the famiglie she formed.
 * The posizione is the one in the famiglia d'origine: null for a persona at the top of the albero.
 */
final readonly class RamoPersona
{
    /**
     * @param  list<RamoFamiglia>  $famiglie
     */
    public function __construct(
        public Persona $persona,
        public ?string $posizione,
        public array $famiglie,
    ) {}
}
