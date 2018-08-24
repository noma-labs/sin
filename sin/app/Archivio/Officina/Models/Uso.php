<?php

namespace App\Officina\Models;

use Illuminate\Database\Eloquent\Model;
use App\Officina\Models\Prenotazioni;

class Uso extends Model
{
    protected $table = 'usi';
    protected $connection = 'db_officina';
    protected $primaryKey = 'ofus_iden';

    public function prenotazioniUso(){
      return $this->hasMany(Prenotazioni::class, 'uso_id', 'ofus_iden');
    }
}
