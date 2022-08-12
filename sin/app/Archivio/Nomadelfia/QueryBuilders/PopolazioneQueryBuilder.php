<?php

namespace App\Nomadelfia\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;

class PopolazioneQueryBuilder extends Builder
{
    public function presente()
    {
        return $this
            ->selectRaw('persone.*, popolazione.*')
            ->leftJoin('persone', 'persone.id', '=', 'popolazione.persona_id')
            ->leftJoin('persone_posizioni', 'persone_posizioni.persona_id', '=','popolazione.persona_id')
            ->whereNull('popolazione.data_uscita')
            ->whereNull('persone.data_decesso')
            ->where(function ($query){
                $query->where('persone_posizioni.stato', '=', '1')
                    ->orWhereNull('persone_posizioni.stato');
            });
    }


}