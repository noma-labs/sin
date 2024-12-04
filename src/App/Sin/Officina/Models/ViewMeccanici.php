<?php

declare(strict_types=1);

namespace App\Officina\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $nominativo
 * @property string $mansione
 */
final class ViewMeccanici extends Model
{
    protected $table = 'v_lavoratori_meccanica';

    protected $connection = 'db_officina';

    protected $primaryKey = 'persona_id';
}
