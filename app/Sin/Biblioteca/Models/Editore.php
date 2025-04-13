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

    protected $guarded = [];

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

    protected function editore(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(set: fn ($value): array => ['editore' => mb_strtoupper((string) $value)]);
    }
}
