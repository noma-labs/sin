<?php

declare(strict_types=1);

namespace App\Biblioteca\Models;

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

    protected static function newFactory(): ClassificazioneFactory
    {
        return ClassificazioneFactory::new();
    }

    protected function descrizione(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(set: function ($value): array {
            return ['descrizione' => mb_strtoupper($value)];
        });
    }
}
