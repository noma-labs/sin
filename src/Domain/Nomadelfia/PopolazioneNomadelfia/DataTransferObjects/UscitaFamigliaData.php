<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects;

use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Illuminate\Database\Eloquent\Collection;

class UscitaFamigliaData
{
    public Famiglia $famiglia;

    public Collection $componenti;

    public string $data_uscita;
}
