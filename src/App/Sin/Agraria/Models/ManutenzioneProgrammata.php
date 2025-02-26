<?php

namespace App\Agraria\Models;

use Illuminate\Database\Eloquent\Model;

class ManutenzioneProgrammata extends Model
{
    protected $connection = 'db_agraria';
    protected $table = 'manutenzione_programmata';
    protected $guarded = [];
    public $timestamps = false;
}
