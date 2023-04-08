<?php

namespace App\Scuola\Models;

use App\Scuola\QueryBuilders\StudenteQueryBuilder;
use Domain\Nomadelfia\Persona\Models\Persona;

class Studente extends Persona
{
    public function newEloquentBuilder($query): StudenteQueryBuilder
    {
        return new StudenteQueryBuilder($query);
    }

    public function classe()
    {
        return $this->hasMany(Classe::class, 'id', 'classe_id');
    }
}
