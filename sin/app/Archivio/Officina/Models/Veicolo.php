<?php

namespace App\Officina\Models;

use Illuminate\Database\Eloquent\Model;
use App\Officina\Models\Modelli;
use App\Officina\Models\Impiego;
use App\Officina\Models\Tipologia;
use App\Officina\Models\Alimentazioni;
use App\Officina\Models\Prenotazioni;


class Veicolo extends Model
{
  protected $table = 'veicolo';
  protected $connection = 'db_officina';
  protected $primaryKey = "id";
  protected $guarded = [];
  public $timestamps = false;

  public function impieghi() {
    return $this->belongsTo('App\Officina\Models\Impiego');
  }

  public function modello(){
    return $this->hasOne(Modelli::class, 'id', 'modello_id');
  }

  public function impiego(){
    return $this->hasOne(Impiego::class, 'id', 'impiego_id');
  }

  public function tipologia(){
    return $this->hasOne(Tipologia::class, 'id', 'tipologia_id');
  }

  public function alimentazione(){
    return $this->hasOne(Alimentazioni::class, 'id', 'alimentazione_id');
  }

  public function prenotazioni() {
      return $this->hasMany(Prenotazioni::class, 'veicolo_id');
  }

  public function scopePrenotabili($query){
      return $query->where('prenotabile',true);
  }

  //IMPIEGO
  public function scopeInterni($query){
      return $query->where('impiego_id',1);
  }

  public function scopeGrosseto($query){
      return $query->where('impiego_id',2);
  }

  public function scopeViaggiLunghi($query){
      return $query->where('impiego_id',3);
  }

  public function scopePersonali($query){
      return $query->where('impiego_id',4);
  }

  public function scopeRoma($query){
      return $query->where('impiego_id',5);
  }

  // TIPOLOGIA
  public function scopeAutovettura($query){
      return $query->where('tipologia_id',1);
  }

  public function scopeAutocarri($query){
      return $query->where('tipologia_id',2);
  }

  public function scopeFurgoni($query){
      return $query->where('tipologia_id', 7);
  }

  public function scopePulmino($query){
      return $query->where('tipologia_id', 6);
  }
  public function scopeAutobus($query){
      return $query->where('tipologia_id', 3);
  }

  public function scopeMotocicli($query){
    return $query->where('tipologia_id', 10);
  }
}
