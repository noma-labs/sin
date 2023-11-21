<?php

namespace Domain\Nomadelfia\Famiglia\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;

class FamigliaQueryBuilder extends Builder
{

    public function single(): FamigliaQueryBuilder
    {

        return $this->select('persone.nominativo as nome_famiglia', 'persone.*')
            ->from('persone')
            ->join('popolazione', 'popolazione.persona_id', '=', 'persone.id')
            ->whereNotIn('persone.id', function ($query) {
                $query->select('famiglie_persone.persona_id')
                    ->from('famiglie_persone');
            })
            ->whereNull('popolazione.data_uscita')
            ->orderBy('persone.nominativo');

    }

    public function male(): FamigliaQueryBuilder
    {
        return $this->where('persone.sesso', 'M');
    }

    public function female(): FamigliaQueryBuilder
    {
        return $this->where('persone.sesso', 'F');
    }
}
