<?php

declare(strict_types=1);

namespace App\Patente\Models;

use App\Nomadelfia\Persona\Models\Persona;
use Illuminate\Database\Eloquent\Model;

final class Restrizione extends Model
{
    public $increment = false;

    public $keyType = 'string';

    public $timestamps = false;

    protected $connection = 'db_patente';

    protected $table = 'restrizione';

    protected $primaryKey = 'codice';

    protected $guarded = [];

    public function persone()
    {
        return $this->belongsTo(Persona::class, 'persona_id', 'id');
    }

    public function categorie()
    {
        return $this->belongsToMany(CategoriaPatente::class, 'patenti_categorie', 'numero_patente', 'categoria_patente_id')
            ->withPivot('data_rilascio', 'data_scadenza');
    }
}
