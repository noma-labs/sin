<?php

namespace App\Agraria\Models;

use Illuminate\Database\Eloquent\Model;

class ManutenzioneTipo extends Model
{
    protected $connection = 'db_agraria';
    protected $table = 'manutenzione_tipo';
    protected $guarded = ['nome'];
    public $timestamps = false;
}
