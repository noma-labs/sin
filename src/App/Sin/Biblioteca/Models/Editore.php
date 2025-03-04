<?php

declare(strict_types=1);

namespace App\Biblioteca\Models;

use Database\Factories\EditoreFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $editore
 */
final class Editore extends Model
{
    use HasFactory;

    protected $connection = 'db_biblioteca';

    protected $table = 'editore';

    protected $guarded = []; // all the fields are mass assignabe

    public function setEditoreAttribute($value): void
    {
        $this->attributes['editore'] = mb_strtoupper($value);
    }

    public function libri()
    {
        return $this->belongsToMany(Libro::class, 'editore_libro', 'editore_id', 'libro_id');
    }

    protected static function newFactory(): EditoreFactory
    {
        return EditoreFactory::new();
    }

    protected static function boot(): void
    {
        parent::boot();

        self::addGlobalScope('singoli', function (Builder $builder): void {
            $builder->where('tipedi', 'S');
        });
    }
}
