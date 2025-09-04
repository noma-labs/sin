<?php

declare(strict_types=1);

namespace App\Officina\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $codice
 * @property string $note
 */
final class TipoGomme extends Model
{
    public $timestamps = false;

    protected $connection = 'db_officina';

    protected $table = 'tipo_gomme';

    protected $primaryKey = 'id';

    protected $fillable = ['codice', 'note'];

    public function veicoli()
    {
        return $this->belongsToMany(Veicolo::class, 'gomme_veicolo', 'gomme_id', 'veicolo_id');
    }
}
