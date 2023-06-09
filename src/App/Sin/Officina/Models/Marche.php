<?php

namespace App\Officina\Models;

use Illuminate\Database\Eloquent\Model;

class Marche extends Model
{
    protected $connection = 'db_officina';

    protected $table = 'marca';

    protected $primaryKey = 'id';

    public function modelli()
    {
        return $this->hasMany(Modelli::class, 'marca_id');
    }
}
