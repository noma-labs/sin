<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects;

use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;

class EntrataPersonaData
{
    public Persona $persona;

    public string $data_entrata;

    public Posizione $posizione;

    public string $posizione_data;

    public GruppoFamiliare $gruppoFamiliare;

    public string $gruppo_data;

    public ?Stato $stato = null;

    public ?string $stato_data;

    public ?Famiglia $famiglia = null;

    public ?string $famiglia_posizione;

    public ?string $famiglia_data;
}
