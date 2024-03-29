<?php

namespace App\Biblioteca\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $nominativo
 * @property string $data_nascita
 * @property float $eta
 */
class ViewClientiBiblioteca extends Model
{
    protected $connection = 'db_biblioteca';

    protected $table = 'v_clienti_biblioteca';

    protected $primaryKey = 'id';

    public function prestiti()
    {
        return $this->hasMany(Prestito::class, 'cliente_id', 'id');
    }
}
