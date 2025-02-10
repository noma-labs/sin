<?php

declare(strict_types=1);

namespace App\Biblioteca\Models;

use App\Biblioteca\Models\Libro as Libro;
use Database\Factories\ClassificazioneFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $descrizione
 */
final class Classificazione extends Model
{
    use HasFactory;

    protected $connection = 'db_biblioteca';

    protected $table = 'classificazione';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    public function libri()
    {
        return $this->hasMany(Libro::class, 'classificazione_id');
    }

    public function setDescrizioneAttribute($value): void
    {
        $this->attributes['descrizione'] = mb_strtoupper($value);
    }

    protected static function newFactory(): ClassificazioneFactory
    {
        return ClassificazioneFactory::new();
    }
}
