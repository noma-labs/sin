<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects;

use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;
use Illuminate\Database\Eloquent\Collection;

class UscitaFamigliaData
{
    public Famiglia $famiglia;

    public Collection $componenti;

    public string $data_uscita;

}
