<?php

namespace App\Officina\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $marca_id
 * @property string $nome
 */
class Modelli extends Model
{
    protected $connection = 'db_officina';

    protected $table = 'modello';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public $timestamps = false;

    public function marca()
    {
        // return $this->hasOne(Marche::class, 'id', 'marca_id');
        return $this->belongsTo(Marche::class, 'marca_id');
    }

    // mette il nome in maiuscolo quando un nuovo modello viene creato o modificato.
    public function setNomeAttribute($value): void
    {
        $this->attributes['nome'] = strtoupper($value);
    }
}
