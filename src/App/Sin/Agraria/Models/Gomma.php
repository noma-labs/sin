<?php

namespace App\Agraria\Models;

use Illuminate\Database\Eloquent\Model;

class Gomma extends Model
{
    protected $connection = 'db_agraria';

    protected $table = 'gomma';
    protected $fillable = ['nome'];
    public $timestamps = false;
}
