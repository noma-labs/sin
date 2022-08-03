<?php

namespace App\Scuola\Models;

use App\Nomadelfia\Models\Persona;
use App\Scuola\QueryBuilders\StudenteQueryBuilder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


class Studente extends Persona
{
//    public $timestamps = true;
//
//    protected $connection = 'db_nomadelfia';
//    protected $table = 'persone';
//    protected $primaryKey = "id";
//    protected $guarded = [];

    public function newEloquentBuilder($query): StudenteQueryBuilder
    {
        return new StudenteQueryBuilder($query);
    }

    public function classe()
    {
        return $this->hasMany(Classe::class, 'id', 'classe_id');
    }

}
