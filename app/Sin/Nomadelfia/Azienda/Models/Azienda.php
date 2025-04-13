<?php

declare(strict_types=1);

namespace App\Nomadelfia\Azienda\Models;

use App\Traits\Enums;
use Database\Factories\AziendaFactory;
use App\Nomadelfia\Persona\Models\Persona;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $tipo
 * @property string $nome_azienda.
 */
final class Azienda extends Model
{
    use Enums;
    use HasFactory;

    public const MANSIONE_LAVORATORE = 'LAVORATORE';

    public const MANSIONE_RESPONSABILE = 'RESPONSABILE AZIENDA';

    public $timestamps = true;

    protected $connection = 'db_nomadelfia';

    protected $table = 'aziende';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public static function scuola()
    {
        return self::perNome('scuola');
    }

    public static function perNome($nome)
    {
        return self::where('nome_azienda', $nome)->first();
    }

    public function scopeAziende($query)
    {
        return $query->where('tipo', '=', 'azienda');
    }

    public function scopeIncarichi($query)
    {
        return $query->where('tipo', '=', 'incarico');
    }

    public function lavoratori(): BelongsToMany
    {
        return $this->belongsToMany(Persona::class, 'aziende_persone', 'azienda_id', 'persona_id')->withPivot('stato',
            'data_inizio_azienda')->orderBy('mansione', 'asc')->orderBy('persone.nominativo');
    }

    public function lavoratoriAttuali(): BelongsToMany
    {
        return $this->lavoratori()->wherePivotIn('stato', ['Attivo', 'Sospeso'])->withPivot('data_inizio_azienda',
            'mansione',
            'stato');
    }

    public function lavoratoriStorici(): BelongsToMany
    {
        return $this->lavoratori()->wherePivot('stato',
            '=', 'Non Attivo')->withPivot('data_fine_azienda', 'stato');
    }

    public function isIncarico(): bool
    {
        return $this->tipo === 'incarico';
    }

    public function isAzienda(): bool
    {
        return $this->tipo === 'azienda';
    }

    protected static function boot(): void
    {
        parent::boot();

        self::addGlobalScope('order', function (Builder $builder): void {
            $builder->orderby('nome_azienda');
        });
    }

    protected static function newFactory()
    {
        return AziendaFactory::new();
    }
}
