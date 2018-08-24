<?php

namespace App\Nomadelfia\Models;

use Illuminate\Database\Eloquent\Model;
use App\Nomadelfia\Models\Persona;

class Incarico extends Model
{
  protected $connection = 'db_nomadelfia';
  protected $table = 'organi_constituzionali';
  protected $primaryKey = "id";

  public function incaricati(){
    return $this->belongsToMany(Persona::class,'organi_constituzionali_persone','organo_constituzionale_id','persona_id');
  }

}