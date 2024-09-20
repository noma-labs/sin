<?php

namespace App\Scuola\Models;

use App\Scuola\QueryBuilders\StudenteQueryBuilder;
use Database\Factories\PersonaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Studente extends Model
{
    use HasFactory;

    protected $connection = 'db_nomadelfia';

    protected $table = 'persone';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $guarded = [];

    public function newEloquentBuilder($query): StudenteQueryBuilder
    {
        return new StudenteQueryBuilder($query);
    }

    protected static function newFactory()
    {
        return PersonaFactory::new();
    }

    public function classe()
    {
        return $this->hasMany(Classe::class, 'id', 'classe_id');
    }
}
