<?php

namespace Domain\Nomadelfia\Famiglia\QueryBuilders;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class FamigliaQueryBuilder extends Builder
{
    public function notAlreadyMarried(): FamigliaQueryBuilder
    {

        return $this->select('persone.nominativo as nome_famiglia', 'persone.*')
            ->from('persone')
            ->join('popolazione', 'popolazione.persona_id', '=', 'persone.id')
            ->whereNotIn('persone.id', function ($query): void {
                $query->select('famiglie_persone.persona_id')
                    ->from('famiglie_persone')
                    ->where('famiglie_persone.posizione_famiglia', 'CAPO FAMIGLIA')
                    ->orWhere('famiglie_persone.posizione_famiglia', 'MOGLIE');
            })
            ->whereNull('popolazione.data_uscita')
            ->orderBy('persone.nominativo');

    }

    public function single(): FamigliaQueryBuilder
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

    public function male(): FamigliaQueryBuilder
    {
        return $this->where('persone.sesso', 'M');
    }

    public function female(): FamigliaQueryBuilder
    {
        return $this->where('persone.sesso', 'F');
    }

    public function maggiorenni(): FamigliaQueryBuilder
    {
        $data = Carbon::now()->subYears(18)->toDatestring();

        return $this->where('persone.data_nascita', '<', $data);
    }
}
