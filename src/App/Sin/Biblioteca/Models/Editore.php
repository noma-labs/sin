<?php

namespace App\Biblioteca\Models;

use App\Biblioteca\Models\Libro as Libro;
use Database\Factories\EditoreFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $editore
 */
class Editore extends Model
{
    use HasFactory;

    protected $connection = 'db_biblioteca';

    protected $table = 'editore';

    protected $guarded = []; // all the fields are mass assignabe

    protected static function newFactory(): EditoreFactory
    {
        return EditoreFactory::new();
    }

    public function setEditoreAttribute($value): void
    {
        $this->attributes['editore'] = strtoupper($value);
    }

    public function libri()
    {
        return $this->belongsToMany(Libro::class, 'editore_libro', 'editore_id', 'libro_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('singoli', function (Builder $builder): void {
            $builder->where('tipedi', 'S');
        });
    }
}
