<?php

declare(strict_types=1);

namespace App\Biblioteca\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $lettere
 * @property float $numeri
 *
 * @method Builder|static MaxForLettere(string $lettere)
 */
final class ViewCollocazione extends Model
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

    public function scopeNumeri($query, string $lettere)
    {
        return $query->select('numeri')->where('lettere', $lettere)->orderBy('numeri');
    }

    public function scopeMaxForLettere($query, string $lettere): float
    {
        // return the max number for the lettere (e.g. App\ViewCollocazione::MaxForLettere("AAA"))
        return $query->numeri($lettere)->get()->max()->numeri;
    }

    public function scopeNumeriMancanti($query, string $lettere): array
    {
        $numeri = $query->numeri($lettere)->get()->toArray();
        $max = $query->numeri($lettere)->get()->max()->numeri;
        $arr2 = range(1, $max);

        return array_diff($numeri, $arr2);
    }
}
