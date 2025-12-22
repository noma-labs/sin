<?php

declare(strict_types=1);

namespace App\Nomadelfia\Famiglia\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;

final class FamigliaQueryBuilder extends Builder
{
    public function notAlreadyMarried(): self
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

    public function male(): self
    {
        return $this->where('persone.sesso', 'M');
    }

    public function female(): self
    {
        return $this->where('persone.sesso', 'F');
    }

    public function maggiorenni(): self
    {
        $data = \Illuminate\Support\Facades\Date::now()->subYears(18)->toDatestring();

        return $this->where('persone.data_nascita', '<', $data);
    }
}
