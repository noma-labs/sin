<?php

declare(strict_types=1);

namespace App\Scuola\Models;

use Database\Factories\ElaboratoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string|null $collocazione
 * @property string|null $anno_scolastico
 * @property string $titolo
 * @property string $classi
 * @property string $file_path
 * @property int    $file_size
 * @property string $file_hash
 * @property string $dimensione
 * @property string $rilegatura
 * @property string $note
 * @property Carbon created_at
 * @property Carbon $updated_at
 * @property int    $libro_id
 */
final class Elaborato extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $connection = 'db_scuola';

    protected $table = 'elaborati';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public function studenti(): BelongsToMany
    {
        return $this->belongsToMany(Studente::class, 'db_scuola.elaborati_studenti', 'elaborato_id', 'studente_id')->orderby('nominativo');
    }

    public function coordinatori(): BelongsToMany
    {
        return $this->belongsToMany(Coordinatore::class, 'db_scuola.elaborati_coordinatori', 'elaborato_id', 'coordinatore_id')->orderby('nominativo');
    }

    protected static function newFactory()
    {
        return ElaboratoFactory::new();
    }
}
