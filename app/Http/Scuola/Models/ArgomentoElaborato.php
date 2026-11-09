<?php

declare(strict_types=1);

namespace App\Scuola\Models;

use Database\Factories\ArgomentoElaboratoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * An argomento of an elaborato, with the materia it belongs to (optional).
 *
 * @property int $id
 * @property int $elaborato_id
 * @property string $argomento
 * @property string|null $materia
 */
final class ArgomentoElaborato extends Model
{
    /** @use HasFactory<ArgomentoElaboratoFactory> */
    use HasFactory;

    protected $connection = 'db_scuola';

    protected $table = 'elaborati_argomenti';

    protected $guarded = [];

    /**
     * @return BelongsTo<Elaborato, $this>
     */
    public function elaborato(): BelongsTo
    {
        return $this->belongsTo(Elaborato::class, 'elaborato_id');
    }

    protected static function newFactory(): ArgomentoElaboratoFactory
    {
        return ArgomentoElaboratoFactory::new();
    }
}
