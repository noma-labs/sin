<?php

namespace App\Officina\Models;

use Illuminate\Database\Eloquent\Model;

class ViewClienti extends Model
{
    protected $table = 'v_clienti_meccanica';

    protected $connection = 'db_officina';

    protected $primaryKey = 'id';

    public function aziende()
    {
      return $this->belongsToMany('App\Officina\Models\AziendeNoma', 'aziende_persone', 'persona_id', 'azienda_id');
    }

    public function prenotazioniMeccanico()
    {
      return $this->hasMany(Prenotazioni::class, 'meccanico_id', 'id');
    }
}
