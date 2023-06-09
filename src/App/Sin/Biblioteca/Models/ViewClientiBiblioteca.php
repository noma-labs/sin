<?php

namespace App\Biblioteca\Models;

use Illuminate\Database\Eloquent\Model;

class ViewClientiBiblioteca extends Model
{
    protected $connection = 'db_biblioteca';

    protected $table = 'v_clienti_biblioteca';

    protected $primaryKey = 'id';

    /**
     * ritorna tutti i presiti del cliente
     */
    public function prestiti()
    {
        return $this->hasMany(Prestito::class, 'cliente_id', 'id');
    }

    //  public function setNominativoAttribute($value) {
    //       $this->attributes['nominativo'] = strtoupper($value);
    //  }

}
