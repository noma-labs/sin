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
    return $this->belongsToMany(Persona::class,'famiglie_persone','famiglia_id','persona_id')
                ->withPivot("posizione_famiglia");
  }

  public function capofamiglia(){
    return $this->belongsToMany(Persona::class,'famiglie_persone','famiglia_id','persona_id')
                ->wherePivot('posizione_famiglia','CAPO FAMIGLIA');
  }

  public function capifamiglia() {
      return $this->capofamiglia()->wherePivot('posizione_famiglia', "CAPO FAMIGLIA");
  }

  public function moglie(){
    return $this->belongsToMany(Persona::class,'famiglie_persone','famiglia_id','persona_id')
                ->wherePivot('posizione_famiglia','MOGLIE')
                ->first();
  }

  public function figliAttuali(){
    return $this->belongsToMany(Persona::class,'famiglie_persone','famiglia_id','persona_id')
                ->wherePivotIn('posizione_famiglia',['FIGLIO NATO','FIGLIO ACCOLTO'])
                ->wherePivot('stato',"=",'1')
                ->orderBy('data_nascita');
  }

  public function single(){
    return $this->belongsToMany(Persona::class,'famiglie_persone','famiglia_id','persona_id')
                ->wherePivot('posizione_famiglia','SINGLE');
  }

  public  function scopeByNucleoFamigliare($quey){
    // return $this->whereHas('componenti',function($query) use($nucleo){
    //   $query->where('nucleo_famigliare_id',$nucleo->id);
    // });
  }

}


