<?php

namespace App\Scuola\Models;

use Illuminate\Database\Eloquent\Model;

class Anno extends Model
{

    public $timestamps = true;

    protected $connection = 'db_scuola';
    protected $table = 'anno';
    protected $primaryKey = "id";
    protected $guarded = [];

    // TODO Create anno scolastico
//if (!Str::contains($annoScolastico, '/')){
//throw CouldNotAssignAlunno::isNotValidAnno($annoScolastico);
//}

    public function classi()
    {
        return $this->hasMany(Classe::class, 'anno_id', 'id');
    }

    public function aggiungiClasse(ClasseTipo $tipo): Classe
    {
        return $this->classi()->create($this->id, ['tipo_id' => $tipo->id]);
    }

}
