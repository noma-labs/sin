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
 * @property string $titolo
 * @property string $collocazione
 * @property string $deleted_note
 * @property string $note
 * @property int $classificazione_id
 * @property int $tobe_printed
 * @property int $ID_EDITORE
 * @property int $ID_AUTORE
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

    public function inPrestito(): bool
    {
        $prestiti = $this->prestiti()->where('in_prestito', 1)->get();

        return count($prestiti) > 0;
    }

    protected static function newFactory()
    {
        return LibroFactory::new();
    }

    protected function scopeTobePrinted($query)
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
}
