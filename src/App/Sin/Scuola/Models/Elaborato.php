<?php

namespace App\Scuola\Models;

use Illuminate\Database\Eloquent\Model;

class Elaborato extends Model
{
    public $timestamps = true;

    protected $connection = 'db_scuola';

    protected $table = 'elaborati';

    protected $primaryKey = 'id';

    protected $guarded = [];
}
