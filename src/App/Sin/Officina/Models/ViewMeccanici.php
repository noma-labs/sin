<?php

namespace App\Officina\Models;

use Illuminate\Database\Eloquent\Model;

use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\Azienda\Models\Azienda;


class ViewMeccanici extends Model
{
  protected $table = 'v_lavoratori_meccanica';
  protected $connection = 'db_officina';
  protected $primaryKey = "persona_id";

  // public function lavoratori(){
  //   return $this->hasOne(Persona::class,'persona_id');
  // }

  // public function azienda(){
  //   return $this->hasOne(Azienda::class,'azienda_id');
  // }

}