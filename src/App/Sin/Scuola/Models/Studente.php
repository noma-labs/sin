<?php

declare(strict_types=1);

namespace App\Scuola\Models;

use App\Scuola\QueryBuilders\StudenteQueryBuilder;
use Database\Factories\PersonaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        return $this->belongsToMany(Classe::class, 'db_scuola.alunni_classi', 'persona_id','classe_id');
    }

    public function elaborati(): HasMany
    {
        return $this->hasMany(Elaborato::class, 'id', 'studente_id');
    }

    protected static function newFactory()
    {
        return PersonaFactory::new();
    }


}
