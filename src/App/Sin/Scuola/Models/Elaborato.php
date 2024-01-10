<?php

namespace App\Scuola\Models;

class Elaborato
{
    public $timestamps = true;

    protected $connection = 'db_scuola';

    protected $table = 'elaborati';

    protected $primaryKey = 'id';

    protected $guarded = [];
}
