<?php

namespace App\Rtn\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Modello per la table trasmissioni_tv
 */
class Trasmissione extends Model
{
    protected $connection = 'db_rtn';

    protected $table = 'trasmissioni_tv';

    protected $primaryKey = 'id_trasmissioni_tv';

    public $timestamps = false;

    protected $guarded = [];

    public static function serie()
    {
        return DB::connection('db_rtn')->table('trasmissioni_tv')->select('serie_tv')->distinct()->orderBy('serie_tv', 'asc')->get();
    }
}
