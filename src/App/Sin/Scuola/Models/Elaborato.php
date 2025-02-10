<?php

declare(strict_types=1);

namespace App\Scuola\Models;

use Database\Factories\ElaboratoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

/**
 * @property string $file_path
 * @property string $cover_image_path
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

    public function getFilePath(): ?string
    {
        if (! Storage::disk('scuola')->exists($this->file_path)) {
            return null;
        }

        return Storage::disk('scuola')->url($this->file_path);
    }

    public function getCoverImagePath(): ?string
    {
        if ($this->cover_image_path === null) {
            return null;
        }
        if (! Storage::disk('public')->exists($this->cover_image_path)) {
            return null;
        }

        return Storage::disk('public')->url($this->cover_image_path);
    }

    protected static function newFactory()
    {
        return ElaboratoFactory::new();
    }
}
