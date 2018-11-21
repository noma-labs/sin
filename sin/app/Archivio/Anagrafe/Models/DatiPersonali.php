<?php

namespace App\Anagrafe\Models;
use Carbon;
use App\Nomadelfia\Models\Persona;

use Illuminate\Database\Eloquent\Model;

class DatiPersonali extends Model
{
  protected $connection = 'db_anagrafe';
  protected $table = 'dati_personali';
  public $timestamps = false;
  protected $primaryKey = "persona_id";

  protected $guarded = [];

  /**
   * Ritorna la persona 
   * @author Davide Neri
   **/
  public function persona(){
    return $this->hasOne(Persona::class,   'id', 'persona_id');
  }


  public function scopeDonne($query)
  {
     return $this->where('sesso','F');
  }

  public function scopeUomimi($query)
  {
     return $this->where('sesso','M');
  }

  public function scopeMaggiorenni($query)
  {
     $date = Carbon::now()->subYears(18)->toDatestring();
     return $query->where('data_nascita', "<=", $date);
  }
}


