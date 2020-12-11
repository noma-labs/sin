<?php

namespace App\Nomadelfia\Models;

use Illuminate\Database\Eloquent\Model;
use App\Nomadelfia\Models\Persona;
use Illuminate\Support\Facades\DB;

class Categoria extends Model
{
  protected $connection = 'db_nomadelfia';
  protected $table = 'categorie';
  protected $primaryKey = "id";

  public function isPersonaInterna(){
    return $this->nome == "Persona Interna";
  }

  // TODO: delete this method and use raw query
  public function persone(){
    return $this->belongsToMany(Persona::class,'persone_categorie', 'categoria_id', 'persona_id')
                ->withPivot("stato")
                ->orderby("nominativo");
  }

  /**
   * Ritorna la posizione dal nome (interno, esterno, collaboratore)
   * 
   * @author: Davide Neri
   */
  public static function perNome($nome){
    
    $mapNamesToDB = [
        "interno"=> "Persona Interna",
        "esterno"=>"Persona esterna",
        "collaboratore"=>"Collaboratore Esterno"
    ];
    return static::where('nome',$mapNamesToDB[$nome])->first();
  }

  public function personeAttuale(){
    $res =DB::connection('db_nomadelfia')->select(
      DB::raw(" SELECT categorie.nome, persone.*
                FROM persone
                INNER JOIN persone_categorie ON persone_categorie.persona_id = persone.id
                INNER JOIN categorie ON categorie.id = persone_categorie.categoria_id
                WHERE persone.stato = '1' AND persone_categorie.stato = '1' and persone_categorie.categoria_id = :categoria
                ORDER by persone.sesso, persone.nominativo"), array("categoria"=> $this->id));
    return $res;
  }
}
