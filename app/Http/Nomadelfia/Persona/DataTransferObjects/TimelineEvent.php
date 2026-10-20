<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\DataTransferObjects;

use App\Anagrafe\Persona\Models\Persona;
use Carbon\Carbon;

/**
 * An important event in the life of a person inside Nomadelfia, shown in the timeline of the person profile.
 */
final readonly class TimelineEvent
{
    public const CATEGORIA_ANAGRAFE = 'anagrafe';

    public const CATEGORIA_COMUNITA = 'comunita';

    public const CATEGORIA_FAMIGLIA = 'famiglia';

    public function __construct(
        public Carbon $data,
        public int $eta,
        public string $categoria,
        public string $etichetta,
        public string $titolo,
        public ?string $dettaglio = null,
        public ?Persona $persona = null,
        public bool $importante = false,
    ) {}
}
