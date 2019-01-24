<?php

namespace App\Nomadelfia\Models;

use Illuminate\Database\Eloquent\Model;
use App\Nomadelfia\Models\Persona;

class Categoria extends Model
{
  protected $connection = 'db_nomadelfia';
  protected $table = 'categorie';
  protected $primaryKey = "id";

  public function persone(){
    return $this->hasMany(Persona::class, 'categoria_id', 'id');
  }

  // public function persone(){
  //   return $this->belongsToMany(Persona::class,'persone_categorie', 'categoria_id', 'persona_id');
  // }


}
