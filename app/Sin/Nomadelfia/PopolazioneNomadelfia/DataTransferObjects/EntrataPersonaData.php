<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects;

use App\Nomadelfia\Famiglia\Models\Famiglia;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Origine;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Stato;
use Carbon\Carbon;

final class EntrataPersonaData
{
    public Persona $persona;

    public Carbon $data_entrata;

    public Posizione $posizione;

    public Carbon $posizione_data;

    public GruppoFamiliare $gruppoFamiliare;

    public Carbon $gruppo_data;

    public ?Stato $stato = null;

    public ?Carbon $stato_data = null;

    public ?Famiglia $famiglia = null;

    public ?string $famiglia_posizione = null;

    public Origine $origine;
}
