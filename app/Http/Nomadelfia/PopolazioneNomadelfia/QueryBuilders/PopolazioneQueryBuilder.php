<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\QueryBuilders;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

final class PopolazioneQueryBuilder extends Builder
{
    public function presentAt(Carbon $date): Builder
    {
        return $this->where('popolazione.data_entrata', '<=', $date)
            ->where(function ($query) use ($date): void {
                $query->whereNull('popolazione.data_uscita')->orWhere('popolazione.data_uscita', '>=', $date);
            });

    }

    public function presente()
    {
        return $this
            ->selectRaw('persone.*, popolazione.*')
            ->leftJoin('persone', 'persone.id', '=', 'popolazione.persona_id')
            ->leftJoin('persone_posizioni', 'persone_posizioni.persona_id', '=', 'popolazione.persona_id')
            ->whereNull('popolazione.data_uscita')
            ->whereNull('persone.data_decesso')
            ->where(function ($query): void {
                $query->where('persone_posizioni.stato', '=', '1')
                    ->orWhereNull('persone_posizioni.stato');
            });
    }

    public function presenteByNomeCognomeNominativo(string $term)
    {
        return $this->presente()
            ->where(function ($query) use ($term): void {
                $query->where('nominativo', 'LIKE', "$term%")
                    ->orWhere('nome', 'LIKE', "$term%")
                    ->orWhere('cognome', 'LIKE', "$term%");

            });
    }

    public function stats()
    {
        $expression = DB::raw('With pop_eta AS (
                        SELECT persone.*, p.data_entrata, TIMESTAMPDIFF(YEAR, persone.data_nascita, CURDATE()) as eta
                        FROM persone
                        INNER join popolazione p ON p.persona_id = persone.id
                        where p.data_uscita is NULL and persone.data_decesso IS NULL and persone.id != 0
                     ) select min(eta) as min, max(eta) as max , TRUNCATE(avg(eta),0) as avg , VARIANCE(eta) as var from pop_eta;');

        return DB::connection('db_nomadelfia')->select(
            $expression->getValue(DB::connection()->getQueryGrammar())
        )[0];
    }

    // with pop as (
    // SELECT persone.*, p.data_entrata, TIMESTAMPDIFF(YEAR, persone.data_nascita, CURDATE()) as eta
    // FROM persone
    // INNER join popolazione p ON p.persona_id = persone.id
    // where data_uscita is NULL
    // ) select
    //    case
    //    when eta between 0 and 18 then '0-18'
    //    when eta between 18 and 21 then '18-21'
    //    when eta between 21 and 40 then '21-40'
    //    when eta between 40 and 50 then '40-50'
    //    when eta between 50 and 70 then '50-70'
    //    when eta between 70 and 100 then '70-100'
    //    else 'OTHERS'
    //    end as r,
    //    count(1) as `Count`
    // from pop
    // group by r;

}
