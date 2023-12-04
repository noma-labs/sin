<?php

namespace App\Biblioteca\Models;

use App\Biblioteca\Models\Libro as Libro;
use Database\Factories\AutoreFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $autore
 */
class Autore extends Model
{
    protected $connection = 'db_biblioteca';

    protected $table = 'autore';

    protected $primaryKey = 'id';

    use HasFactory;

    protected $guarded = []; // all the fields are mass assignabe

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('tipaut', function (Builder $builder) {
            $builder->where('tipaut', 'S');
        });
    }

    protected static function newFactory(): AutoreFactory
    {
        return AutoreFactory::new();
    }

    public function setAutoreAttribute($value)
    {
        $this->attributes['autore'] = strtoupper($value);
    }

    public function libri()
    {
        return $this->belongsToMany(Libro::class, 'autore_libro', 'autore_id', 'libro_id');
    }
}
