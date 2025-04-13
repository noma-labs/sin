<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects;

use Carbon\Carbon;
use App\Nomadelfia\Famiglia\Models\Famiglia;
use Illuminate\Database\Eloquent\Collection;

final class UscitaFamigliaData
{
    public Famiglia $famiglia;

    public Collection $componenti;

    public Carbon $data_uscita;
}
