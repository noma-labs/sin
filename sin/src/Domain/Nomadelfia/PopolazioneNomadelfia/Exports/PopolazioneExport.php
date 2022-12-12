<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Exports;

use Domain\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneAttuale;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;

class PopolazioneExport implements FromCollection
{
    use Exportable;

    public $persone;

    public function __construct($persone)
    {
        $this->persone = $persone;
    }

    public function collection()
    {
        return PopolazioneAttuale::whereIn('id', $this->persone)->get();
    }
}