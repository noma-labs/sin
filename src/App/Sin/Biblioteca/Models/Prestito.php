<?php

namespace App\Biblioteca\Models;

use App\Biblioteca\Models\Libro as Libro;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Prestito extends Model
{
    protected $connection = 'db_biblioteca';

    protected $table = 'prestito';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    public function cliente(): HasOne
    {
        return $this->hasOne(Persona::class, 'id', 'cliente_id');
    }

    public function bibliotecario(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'bibliotecario_id', 'id'); //->withTrashed();
    }

    public function libro(): BelongsTo
    {
        return $this->belongsTo(Libro::class, 'libro_id')->withTrashed();
    }

    public function scopeInPrestito($query)
    {
        return $query->where('in_prestito', 1);
    }

    public function scopeRestituiti($query)
    {
        return $query->where('in_prestito', 0);
    }
}
