<?php

namespace App\Scuola\Models;

use App\Nomadelfia\Models\Persona;
use App\Scuola\QueryBuilders\StudenteQueryBuilder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


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
