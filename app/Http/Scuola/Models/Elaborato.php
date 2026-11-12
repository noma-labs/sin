<?php

declare(strict_types=1);

namespace App\Scuola\Models;

use Database\Factories\ElaboratoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $file_path
 * @property string $cover_image_path
 * @property string $collocazione
 * @property string|null $classi
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

    /**
     * The ciclo of the classi of the elaborato: prescuola, elementari, medie, superiori,
     * tutti when the classi belong to more than one ciclo, null when no classe has a ciclo.
     */
    public function ciclo(): ?string
    {
        $cicli = collect(explode(',', (string) $this->classi))
            ->map(fn (string $classe): string => mb_strtolower(mb_trim($classe)))
            ->map(fn (string $classe): ?string => match (true) {
                $classe === 'prescuola' => 'prescuola',
                $classe === 'tutti i cicli' => 'tutti',
                str_ends_with($classe, 'elementare') => 'elementari',
                $classe === 'medie', str_ends_with($classe, 'media') => 'medie',
                $classe === 'superiori', str_ends_with($classe, 'superiore') => 'superiori',
                default => null,
            })
            ->filter()
            ->unique();

        return match ($cicli->count()) {
            0 => null,
            1 => $cicli->first(),
            default => 'tutti',
        };
    }

    public function cicloLabel(): ?string
    {
        return match ($this->ciclo()) {
            'prescuola' => 'Prescuola',
            'elementari' => 'Elementari',
            'medie' => 'Medie',
            'superiori' => 'Superiori',
            'tutti' => 'Più cicli',
            default => null,
        };
    }

    protected static function newFactory()
    {
        return ElaboratoFactory::new();
    }
}
