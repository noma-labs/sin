<?php
namespace App\Patente\Models;

use Illuminate\Database\Eloquent\Model;

class ViewClientiSenzaPatente extends Model
{
    protected $table = 'v_cliente_patente';
    protected $connection = 'db_patente';
    protected $primaryKey = "persona_id";

    // public function aziende(){
    //   return $this->belongsToMany('App\Officina\Models\AziendeNoma', 'aziende_persone', 'persona_id', 'azienda_id');
    // }

    // public function prenotazioniMeccanico(){
    //   return $this->hasMany(Prenotazioni::class, 'meccanico_id', 'id');
    // }

}
