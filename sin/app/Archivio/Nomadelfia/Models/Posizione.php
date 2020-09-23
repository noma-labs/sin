<?php

namespace App\Nomadelfia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Excpetions\PosizioneDoesNotExist;

class Posizione extends Model
{
  protected $connection = 'db_nomadelfia';
  protected $table = 'posizioni';
  protected $primaryKey = "id";

  /**
   * Ordina (di default) le posizioni secondo la colonna ordinamento
   * 
   * @author: Davide Neri
   */
  protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('ordinamento', function (Builder $builder) {
          $builder->orderBy('ordinamento');
      });
    }

  public function persone(){
    return $this->belongsToMany(Persona::class,'persone_posizioni', 'posizione_id', 'persona_id')
                ->withPivot("stato")
                ->orderby("nominativo");
  }

  public function personeAttuale(){
    return $this->persone()->where("persone_posizioni.stato","1");
  }

  /**
     * Find a Posizione by its name
     *
     * @param string $name abbreviato
     * @param string|null $guardName
     *
     * @throws \App\Nomadelfia\Exceptions\PosizioneDoesNotExist
     *
     * @return  \App\Nomadelfia\Models\Posizione
     */
    public static function find(string $name): Posizione
    {
        $posizione = Posizione::where("abbreviato", $name)->first();
        if (! $posizione) {
            throw PosizioneDoesNotExist::create($name);
        }
        return $posizione;
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
        "uscito" =>"DADE",
    ];
    return static::where('abbreviato',$mapNamesToDB[$nome])->first();
  }

}
