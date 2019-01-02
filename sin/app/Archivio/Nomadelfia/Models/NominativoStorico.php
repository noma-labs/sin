<?php

namespace App\Nomadelfia\Models;

use Illuminate\Database\Eloquent\Model;
use App\Nomadelfia\Models\Persona;

class NominativoStorico extends Model
{
  protected $connection = 'db_nomadelfia';
  protected $table = 'nominativi_storici';
  protected $primaryKey = "persona_id";

}
