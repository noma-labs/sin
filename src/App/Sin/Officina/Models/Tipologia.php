<?php

namespace App\Officina\Models;

use Illuminate\Database\Eloquent\Model;

class Tipologia extends Model
{
  protected $table = 'tipologia';
  protected $connection = 'db_officina';
  protected $primaryKey = "id";

  public function veicoli(){
    return $this->hasMany('App\Officina\Models\Veicolo', 'tipologia_id', 'id');
  }

}
