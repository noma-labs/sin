<?php

declare(strict_types=1);

namespace App\Officina\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $nome
 */
final class Tipologia extends Model
{
    protected $table = 'tipologia';

    protected $connection = 'db_officina';

    protected $primaryKey = 'id';

    public function veicoli()
    {
        return $this->hasMany(Veicolo::class, 'tipologia_id', 'id');
    }
}
