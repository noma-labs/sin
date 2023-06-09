<?php

namespace App\Biblioteca\Models;

use Illuminate\Database\Eloquent\Model;

/*
select `archivio_biblioteca`.`libro`.`collocazione` AS `COLLOCAZIONE`,substr(`archivio_biblioteca`.`libro`.`collocazione`,1,3)
AS `lettere`,cast(substr(`archivio_biblioteca`.`libro`.`collocazione`,4,3) as unsigned) AS `numeri` from `archivio_biblioteca`.`libro`
*/
class ViewCollocazione extends Model
{
    protected $connection = 'db_biblioteca';

    protected $table = 'v_colloc_split';

    public function scopeLettere($query)
    {
        return $query->select('lettere')->whereRaw('LENGTH(lettere) = 3')
            ->orWhereRaw('lettere = 000')->groupBy('lettere');
    }

    public function scopeTotal($query)
    {
        return $query->lettere()->get()->count();
    }

    public function scopeNumeri($query, $lettere)
    {
        return $query->select('numeri')->where('lettere', $lettere)->orderBy('numeri');
    }

    public function scopeMaxForLettere($query, $lettere)
    {
        // return the max number for the lettere (e.g. App\ViewCollocazione::MaxForLettere("AAA"))
        return $query->numeri($lettere)->get()->max()->numeri;
    }

    public function scopeNumeriMancanti($query, $lettere)
    {
        $numeri = $query->numeri($lettere)->get()->toArray();
        $max = $query->numeri($lettere)->get()->max()->numeri;
        $arr2 = range(1, $max);

        return array_diff($numeri, $arr2);
    }
}
