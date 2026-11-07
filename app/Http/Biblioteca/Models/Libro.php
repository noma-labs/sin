<?php

declare(strict_types=1);

namespace App\Biblioteca\Models;

use App\Traits\Enums;
use App\Traits\SortableTrait;
use Database\Factories\LibroFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $titolo
 * @property string $collocazione
 * @property string $deleted_note
 * @property string $note
 * @property int $classificazione_id
 * @property int $tobe_printed
 * @property int $ID_EDITORE
 * @property int $ID_AUTORE
 * @property string|null $autore
 * @property string|null $editore
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Autore> $autori
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Editore> $editori
 */
final class Libro extends Model
{
    use Enums;
    use HasFactory;
    use SoftDeletes;
    use SortableTrait;

    protected $connection = 'db_biblioteca';

    protected $table = 'libro';

    protected $primaryKey = 'id';

    protected $guarded = []; // all the fields are mass assignabe

    protected $enumCategoria = [
        'piccoli',
        'elementari',
        'medie',
        'superiori',
        'adulti',
    ];

    protected $enumCritica = [
        1,
        2,
        3,
        4,
        5,
    ];

    public function getLogNameToUse(string $eventName = ''): string
    {
        return 'biblioteca';
    }

    public function classificazione(): BelongsTo
    {
        return $this->belongsTo(Classificazione::class, 'classificazione_id');
    }

    public function editori(): BelongsToMany
    {
        return $this->belongsToMany(Editore::class, 'editore_libro', 'libro_id', 'editore_id');
    }

    public function autori(): BelongsToMany
    {
        return $this->belongsToMany(Autore::class, 'autore_libro', 'libro_id', 'autore_id');
    }

    public function prestiti(): HasMany
    {
        return $this->hasMany(Prestito::class, 'libro_id');
    }

    /**
     * The legacy `autore` text of the book when it differs from the names of the linked autori.
     */
    public function autoreSuggestion(): ?string
    {
        return $this->legacyValueDifferentFrom($this->autore, $this->autori->map(fn (Autore $autore): string => $autore->autore)->all());
    }

    /**
     * The legacy `editore` text of the book when it differs from the names of the linked editori.
     */
    public function editoreSuggestion(): ?string
    {
        return $this->legacyValueDifferentFrom($this->editore, $this->editori->map(fn (Editore $editore): string => $editore->editore)->all());
    }

    public function inPrestito(): bool
    {
        $prestiti = $this->prestiti()->where('in_prestito', 1)->get();

        return count($prestiti) > 0;
    }

    protected static function newFactory()
    {
        return LibroFactory::new();
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function tobePrinted($query)
    {
        return $query->where('tobe_printed', 1)->orderBy('collocazione');
    }

    protected function scopeEditori($query)
    {
        return $query->select('editore')->groupBy('editori'); // ->orderBY("EDITORE");
    }

    protected function scopeAutori($query)
    {
        return $query->select('autore')->groupBy('autore'); // ->orderBY("AUTORE");
    }

    protected function titolo(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => mb_strtoupper($value),
        );
    }

    protected function casts(): array
    {
        return ['deleted_at' => 'datetime'];
    }

    /**
     * Compares ignoring case, spaces and punctuation, so "ROSSI, MARIO" matches "Rossi Mario".
     *
     * @param  array<int, string>  $linkedNames
     */
    private function legacyValueDifferentFrom(?string $legacyValue, array $linkedNames): ?string
    {
        if ($legacyValue === null || mb_trim($legacyValue) === '') {
            return null;
        }

        $normalize = fn (string $value): string => (string) preg_replace('/[^\p{L}\p{N}]+/u', '', mb_strtoupper($value));

        if ($normalize($legacyValue) === $normalize(implode('', $linkedNames))) {
            return null;
        }

        return $legacyValue;
    }
}
