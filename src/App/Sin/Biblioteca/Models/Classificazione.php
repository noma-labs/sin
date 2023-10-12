<?php

namespace App\Biblioteca\Models;

use App\Biblioteca\Models\Libro as Libro;
use Database\Factories\ClassificazioneFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $descrizione
 */
class Classificazione extends Model
{
    protected $connection = 'db_biblioteca';

    protected $table = 'classificazione';

    protected $primaryKey = 'id';

    use HasFactory;

    protected $guarded = ['id'];

    public function libri()
    {
        return $this->hasMany(Libro::class, 'classificazione_id');
    }

    public function setDescrizioneAttribute($value)
    {
        $this->attributes['descrizione'] = strtoupper($value);
    }

    protected static function newFactory(): ClassificazioneFactory
    {
        return ClassificazioneFactory::new();
    }
}
