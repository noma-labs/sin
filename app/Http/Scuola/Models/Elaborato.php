<?php

declare(strict_types=1);

namespace App\Scuola\Models;

use Database\Factories\ElaboratoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string $file_path
 * @property string $cover_image_path
 * @property string $collocazione
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
