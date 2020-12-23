<?php

namespace App\Nomadelfia\Models;

use Illuminate\Database\Eloquent\Model;
use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Exceptions\StatoDoesNotExists;


class Stato extends Model
{
  protected $connection = 'db_nomadelfia';
  protected $table = 'stati';
  protected $primaryKey = "id";

  public $guarded = ['id'];
  public $timestamps = false;


  public function persone(){
    return $this->belongsToMany(Persona::class,'persone_stati', 'stato_id', 'persona_id')
                 ->withPivot("stato")
                 ->orderby("nominativo");
  }

  public function personeAttuale(){
    return $this->persone()->where("persone_stati.stato","1");
  }

  //usat ???
  public function scopeAttivo($query)
  {
      return $query->where('persone_stati.stato', 1);
  }


   /**
     * Find a STATO by its abbreviato
     *
     * @param string $name
     * @param string|null $guardName
     *
     * @throws \App\Nomadelfia\Exceptions\PosizioneDoesNotExist
     *
     * @return  \App\Nomadelfia\Models\Posizione
     */
    public static function find(string $name): Stato
    {
        $stato = Stato::where("stato", $name)->first();
        if (! $stato) {
            throw StatoDoesNotExists::create($name);
        }
        return $stato;
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
