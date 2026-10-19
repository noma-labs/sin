<?php

declare(strict_types=1);

namespace App\Anagrafe\Famiglia\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;

final class FamigliaQueryBuilder extends Builder
{
    public function notAlreadyMarried(): self
    {

        return $this->select('persone.nominativo as nome_famiglia', 'persone.*')
            ->from('persone')
            ->whereNotIn('persone.id', function ($query): void {
                $query->select('famiglie_persone.persona_id')
                    ->from('famiglie_persone')
                    ->where('famiglie_persone.posizione_famiglia', 'CAPO FAMIGLIA')
                    ->orWhere('famiglie_persone.posizione_famiglia', 'MOGLIE');
            })
            ->whereNull('persone.data_decesso')
            ->whereNull('persone.deleted_at')
            ->orderBy('persone.nominativo');

    }

    public function single(): self
    {

        return $this->select('persone.nominativo as nome_famiglia', 'persone.*', 'persone.id as persona_id')
            ->from('persone')
            ->whereNotIn('persone.id', function ($query): void {
                $query->select('famiglie_persone.persona_id')
                    ->from('famiglie_persone')
                    ->where('famiglie_persone.stato', '=', '1');
            })
            ->whereNull('persone.deleted_at')
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
