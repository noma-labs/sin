<?php

namespace App\Nomadelfia\Models;

use Illuminate\Database\Eloquent\Model;
use App\Nomadelfia\Models\Persona;

class Categoria extends Model
{
  protected $connection = 'db_nomadelfia';
  protected $table = 'categorie';
  protected $primaryKey = "id";


}
