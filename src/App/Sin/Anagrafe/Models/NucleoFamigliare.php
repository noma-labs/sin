<?php

namespace App\Anagrafe\Models;

use Illuminate\Database\Eloquent\Model;

class NucleoFamigliare extends Model
{
  protected $connection = 'db_nomadelfia';

  protected $table = 'nuclei_famigliari';

  protected $primaryKey = 'id';
}
