<?php

declare(strict_types=1);

namespace App\Officina\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $nome
 */
final class Impiego extends Model
{
    protected $table = 'impiego';

    protected $connection = 'db_officina';

    protected $primaryKey = 'id';

    public function veicoli()
    {
        return $this->hasMany(Veicolo::class, 'impiego_id', 'id');
    }
}
