<?php

declare(strict_types=1);

namespace App\Anagrafe\Famiglia\QueryBuilders;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Persona\Models\Persona;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<Famiglia>
 */
final class FamigliaQueryBuilder extends Builder
{
    /**
     * The famiglie with at least one living componente in the nucleo: also the famiglia of a vedova, whose capofamiglia is deceased.
     */
    public function conComponentiInVita(): self
    {
        return $this->whereHas('componenti', function (Builder $query): void {
            $query->where('famiglie_persone.stato', '1')->whereNull('persone.data_decesso');
        });
    }

    /**
     * The famiglie with at least one componente in the nucleo living in Nomadelfia.
     */
    public function inNomadelfia(): self
    {
        return $this->whereHas('componenti', function (Builder $query): void {
            $query->where('famiglie_persone.stato', '1')->whereIn('persone.id', Persona::queryIdsInterni());
        });
    }

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
            ->whereNull('persone.data_decesso')
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
