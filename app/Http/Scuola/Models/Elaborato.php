<?php

declare(strict_types=1);

namespace App\Scuola\Models;

use Database\Factories\ElaboratoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $file_path
 * @property string $cover_image_path
 * @property string $collocazione
 * @property TipoElaborato|null $tipo
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ArgomentoElaborato> $argomenti
 */
final class Elaborato extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $connection = 'db_scuola';

    protected $table = 'elaborati';

    protected $primaryKey = 'id';

    protected $guarded = [];

    /**
     * Eager loads the argomenti ordered by materia, then by argomento.
     *
     * @return array<string, callable(HasMany<ArgomentoElaborato, covariant self>): void>
     */
    public static function argomentiOrdinati(): array
    {
        return ['argomenti' => function (HasMany $query): void {
            $query->orderBy('materia')->orderBy('argomento');
        }];
    }

    public function studenti(): BelongsToMany
    {
        return $this->belongsToMany(Studente::class, 'db_scuola.elaborati_studenti', 'elaborato_id', 'studente_id')->orderby('nominativo');
    }

    public function coordinatori(): BelongsToMany
    {
        return $this->belongsToMany(Coordinatore::class, 'db_scuola.elaborati_coordinatori', 'elaborato_id', 'coordinatore_id')->orderby('nominativo');
    }

    /**
     * @return HasMany<ArgomentoElaborato, covariant $this>
     */
    public function argomenti(): HasMany
    {
        return $this->hasMany(ArgomentoElaborato::class, 'elaborato_id');
    }

    /**
     * The materie of the argomenti of the elaborato, each once.
     *
     * @return Collection<int, string>
     */
    public function materie(): Collection
    {
        return $this->argomenti
            ->flatMap(fn (ArgomentoElaborato $argomento): array => filled($argomento->materia) ? [$argomento->materia] : [])
            ->unique()
            ->sort()
            ->values();
    }

    protected static function newFactory()
    {
        return ElaboratoFactory::new();
    }

    protected function casts(): array
    {
        return [
            'tipo' => TipoElaborato::class,
        ];
    }
}
