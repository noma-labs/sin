<?php

namespace App\Officina\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $nominativo
 * @property string $mansione
 */
class ViewMeccanici extends Model
{
    protected $table = 'v_lavoratori_meccanica';

    protected $connection = 'db_officina';

    protected $primaryKey = 'persona_id';
}
