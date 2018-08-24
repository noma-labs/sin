<?php

namespace App\Biblioteca\Models;
use App\Biblioteca\Models\Libro as Libro;

use Illuminate\Database\Eloquent\Model;

class Classificazione extends Model
{
  protected $connection = 'db_biblioteca';
  protected $table = 'classificazione';
  protected $primaryKey = "id";

  protected $guarded = ["id"];

  public function libri(){
    return $this->hasMany(Libro::class, "classificazione_id");
  }

  public function setDescrizioneAttribute($value) {
       $this->attributes['descrizione'] = strtoupper($value);
  }


}
