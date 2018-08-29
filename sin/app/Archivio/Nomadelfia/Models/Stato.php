<?php

namespace App\Nomadelfia\Models;

use Illuminate\Database\Eloquent\Model;
use App\Nomadelfia\Models\Persona;

class Stato extends Model
{
  protected $connection = 'db_nomadelfia';
  protected $table = 'stati';
  protected $primaryKey = "id";


  public function persone(){
    return $this->belongsToMany(Persona::class,'persone_stati', 'stato_id', 'persona_id');
  }

  /**
   * Ritorna lo stato dal suo nome
   * 
   * @author: Davide Neri
   */

  public static function perNome($nome){
    
    $mapNamesToDB = [
        "sacerdote"=> "SAC",
        "celibe"=>"CEL",
        "mammavocazione"=>"MAV",
        "sposato"=>"SPO",
    ];
    return static::where('stato',$mapNamesToDB[$nome])->first();
  }

}
