<?php

namespace App\Officina\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $codice
 * @property string $note
 */
class TipoGomme extends Model
{
    protected $connection = 'db_officina';

    protected $table = 'tipo_gomme';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = ['codice', 'note'];

    /**
     * ritorna tutti i veicoli con un tipo di gomme
     */
    public function veicoli()
    {
        return $this->belongsToMany(Veicolo::class, 'gomme_veicolo', 'gomme_id', 'veicolo_id');
    }
}
