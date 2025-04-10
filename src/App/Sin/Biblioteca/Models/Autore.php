<?php

declare(strict_types=1);

namespace App\Biblioteca\Models;

use Database\Factories\AutoreFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $autore
 */
final class Autore extends Model
{
    use HasFactory;

    protected $connection = 'db_biblioteca';

    protected $table = 'autore';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public function libri()
    {
        return $this->belongsToMany(Libro::class, 'autore_libro', 'autore_id', 'libro_id');
    }

    protected static function boot(): void
    {
        parent::boot();

        self::addGlobalScope('tipaut', function (Builder $builder): void {
            $builder->where('tipaut', 'S');
        });
    }

    protected static function newFactory(): AutoreFactory
    {
        return AutoreFactory::new();
    }

    protected function autore(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(set: fn ($value): array => ['autore' => mb_strtoupper((string) $value)]);
    }
}
