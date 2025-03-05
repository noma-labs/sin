<?php

declare(strict_types=1);

namespace Domain\Nomadelfia\Famiglia\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;

final class FamigliaQueryBuilder extends Builder
{

    public function single(): self
    {

        return $this->select('persone.nominativo as nome_famiglia', 'persone.*')
            ->from('persone')
            ->join('popolazione', 'popolazione.persona_id', '=', 'persone.id')
            ->whereNotIn('persone.id', function ($query): void {
                $query->select('famiglie_persone.persona_id')
                    ->from('famiglie_persone')
                    ->where('famiglie_persone.stato', '=', '1');
            })
            ->whereNull('popolazione.data_uscita')
            ->orderBy('persone.nominativo');
    }
}
