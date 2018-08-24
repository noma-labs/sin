<?php
namespace App\Nomadelfia\Models;

use Illuminate\Database\Eloquent\Model;

use App\Nomadelfia\Models\Persona;
use App\Traits\Enums;

class Azienda extends Model
{
  use Enums;
  
  protected $connection = 'db_nomadelfia';
  protected $table = 'aziende';
  protected $primaryKey = "id";

  public function lavoratori(){

    return $this->belongsToMany(Persona::class,'aziende_persone','azienda_id','persona_id')->withPivot('stato', 'data_inizio_azienda');
  }

  public function lavoratoriAttuali(){
  	return $this->belongsToMany(Persona::class,'aziende_persone','azienda_id','persona_id')->wherePivotIn('stato', ['Attivo', 'Sospeso'])->withPivot('data_inizio_azienda', 'mansione', 'stato');
  }

  public function lavoratoriStorici(){
  	return $this->belongsToMany(Persona::class,'aziende_persone','azienda_id','persona_id')->wherePivot('stato', '=', 'Non Attivo')->withPivot('data_fine_azienda', 'stato');
  }

  public static function perNome($nome){
    return static::where('nome_azienda', $nome)->first();
  }


}
