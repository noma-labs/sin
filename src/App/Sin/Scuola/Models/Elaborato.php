<?php

namespace App\Scuola\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Elaborato extends Model
{
    public $timestamps = true;

    protected $connection = 'db_scuola';

    protected $table = 'elaborati';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public function studenti(): BelongsToMany
    {
        return $this->belongsToMany(Studente::class, 'db_scuola.elaborati_studenti', 'elaborato_id', 'studente_id',)->orderby('nominativo');
    }

    public function coordinatori(): BelongsToMany
    {
        return $this->belongsToMany(Coordinatore::class, 'db_scuola.elaborati_coordinatori', 'elaborato_id', 'coordinatore_id')->orderby('nominativo');
    }
}
