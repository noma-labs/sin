<?php
namespace App\Officina\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Officina\Models\Uso;
use App\Officina\Models\Veicolo;
use App\Nomadelfia\Models\Persona;

use App\Traits\SortableTrait;

class Prenotazioni extends Model{

  use SoftDeletes;
  use SortableTrait;
  
  protected $table = "prenotazioni";
  protected $connection = "db_officina";
  protected $primareKey = "id";


  protected $guarded = [];
  protected $dates = ['deleted_at'];

  public function uso(){
    return $this->hasOne(Uso::class, 'ofus_iden', 'uso_id');
  }

  public function meccanico(){
    return $this->hasOne(Persona::class, 'id', 'meccanico_id');
  }

  public function cliente(){
      return $this->hasOne(ViewClienti::class, 'id', 'cliente_id');
  }

  public function veicolo(){
    return $this->hasOne(Veicolo::class, 'id', 'veicolo_id');
  }
}
