<?php

namespace App\Agraria\Models;

use Illuminate\Database\Eloquent\Model;

class StoricoOre extends Model
{
    protected $connection = 'db_agraria';
    protected $table = 'storico_ore';
    protected $guarded = [];
    public $timestamps = false;
}
