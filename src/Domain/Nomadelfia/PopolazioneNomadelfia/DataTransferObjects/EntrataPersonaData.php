<?php

declare(strict_types=1);

namespace Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects;

use Carbon\Carbon;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Origine;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;

final class EntrataPersonaData
{
    public Persona $persona;

    public Carbon $data_entrata;

    public Posizione $posizione;

    public Carbon $posizione_data;

    public GruppoFamiliare $gruppoFamiliare;

    public Carbon $gruppo_data;

    public ?Stato $stato = null;

    public ?Carbon $stato_data;

    public ?Famiglia $famiglia = null;

    public ?string $famiglia_posizione;

    public Origine $origine;
}
