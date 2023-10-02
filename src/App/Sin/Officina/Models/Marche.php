<?php

namespace App\Officina\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Marche extends Model
{
    protected $connection = 'db_officina';

    protected $table = 'marca';

    protected $primaryKey = 'id';

    public function modelli(): HasMany
    {
        return $this->hasMany(Modelli::class, 'marca_id');
    }
}
