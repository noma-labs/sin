<?php

namespace App\Nomadelfia\Models;

use Illuminate\Database\Eloquent\Model;
use App\Nomadelfia\Models\Persona;

class Famiglia extends Model
{
  protected $connection = 'db_nomadelfia';
  protected $table = 'famiglie';
  protected $primaryKey = "id";

  protected $guarded = [];

  public function componenti(){
    return $this->belongsToMany(Persona::class,'famiglie_persone','famiglia_id','persona_id');
  }

  public  function scopeByNucleoFamigliare($quey){
    // return $this->whereHas('componenti',function($query) use($nucleo){
    //   $query->where('nucleo_famigliare_id',$nucleo->id);
    // });
  }

}


