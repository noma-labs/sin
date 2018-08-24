<?php

namespace App\Anagrafe\Models;

use Illuminate\Database\Eloquent\Model;

class DatiPersonali extends Model
{
  protected $connection = 'db_anagrafe';
  protected $table = 'dati_personali';
  public $timestamps = false;
  protected $primaryKey = "persona_id";
}