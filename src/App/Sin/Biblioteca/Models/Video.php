<?php

namespace App\Biblioteca\Models;

use App\Traits\SortableTrait;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use SortableTrait;

    protected $connection = 'db_biblioteca';

    protected $table = 'video';

    protected $primaryKey = 'id';

    protected $casts = ['data_registrazione' => 'datetime'];
}
