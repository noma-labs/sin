<?php

namespace App\Officina\Models;

use Illuminate\Database\Eloquent\Model;

class Impiego extends Model
{
  protected $table = 'impiego';
  protected $connection = 'db_officina';
  protected $primaryKey = "id";

  public function veicoli(){
    return $this->hasMany('App\Officina\Models\Veicolo', 'impiego_id', 'id');
  }


}
