<?php

namespace Domain\Nomadelfia\Persona\Models;

use Illuminate\Database\Eloquent\Model;

class NominativoStorico extends Model
{
  protected $connection = 'db_nomadelfia';

  protected $table = 'nominativi_storici';

  protected $primaryKey = 'persona_id';

  protected $guarded = [];
}
