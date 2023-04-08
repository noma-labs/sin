<?php

namespace App\Biblioteca\Models;

use Illuminate\Database\Eloquent\Model;

class ViewLavoratoriBiblioteca extends Model
{
     protected $connection = 'db_biblioteca';

     protected $table = 'v_lavoratori_biblioteca';

     protected $primaryKey = 'persona_id';
}
