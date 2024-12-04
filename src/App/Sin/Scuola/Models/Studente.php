<?php

declare(strict_types=1);

namespace App\Scuola\Models;

use App\Scuola\QueryBuilders\StudenteQueryBuilder;
use Database\Factories\PersonaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Studente extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $connection = 'db_nomadelfia';

    protected $table = 'persone';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public function newEloquentBuilder($query): StudenteQueryBuilder
    {
        return new StudenteQueryBuilder($query);
    }

    public function classe()
    {
        return $this->hasMany(Classe::class, 'id', 'classe_id');
    }

    protected static function newFactory()
    {
        return PersonaFactory::new();
    }
}
