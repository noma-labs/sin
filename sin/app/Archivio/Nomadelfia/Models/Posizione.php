<?php

namespace App\Nomadelfia\Models;

use Illuminate\Database\Eloquent\Model;
use App\Nomadelfia\Models\Persona;

class Posizione extends Model
{
  protected $connection = 'db_nomadelfia';
  protected $table = 'posizioni';
  protected $primaryKey = "id";


  public function persone(){
    return $this->belongsToMany(Persona::class,'persone_posizioni', 'posizione_id', 'persona_id');
  }


  /**
   * Ritorna la posizione dal nome
   * 
   * @author: Davide Neri
   */

  public static function perNome($nome){
    
    $mapNamesToDB = [
        "effettivo"=> "EFFE",
        "postulante"=>"POST",
        "ospite"=>"OSPP",
        "figlio"=>"FIGL",
    ];
    return static::where('abbreviato',$mapNamesToDB[$nome])->first();
  }

}