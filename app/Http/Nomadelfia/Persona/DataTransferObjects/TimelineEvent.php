<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\DataTransferObjects;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use Carbon\Carbon;

/**
 * An important event in the life of a person inside Nomadelfia, shown in the timeline of the person profile.
 * An event without a registered date keeps its place in the timeline through $data, which is not shown.
 * The entrata and the uscita of a period in Nomadelfia (also the decesso that closes it) carry the $periodo and which of
 * its dates they show ($confinePeriodo), so that the date can be corrected from the timeline.
 */
final readonly class TimelineEvent
{
    public const CATEGORIA_ANAGRAFE = 'anagrafe';

    public const CATEGORIA_COMUNITA = 'comunita';

    public const CATEGORIA_FAMIGLIA = 'famiglia';

    public const PERIODO_ENTRATA = 'entrata';

    public const PERIODO_USCITA = 'uscita';

    public function __construct(
        public Carbon $data,
        public int $eta,
        public string $categoria,
        public string $etichetta,
        public string $titolo,
        public ?string $dettaglio = null,
        public ?Persona $persona = null,
        public bool $importante = false,
        public ?Famiglia $famiglia = null,
        public bool $dataRegistrata = true,
        public ?PopolazioneNomadelfia $periodo = null,
        public ?string $confinePeriodo = null,
    ) {}
}
