<?php

namespace App\Nomadelfia\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;

class PopolazioneQueryBuilder extends Builder
{
    public function presente()
    {
        return $this
            ->selectRaw('persone.*, popolazione.*')
            ->join('persone', 'persone.id', '=', 'popolazione.persona_id')
            ->whereNull('popolazione.data_uscita');
    }


}