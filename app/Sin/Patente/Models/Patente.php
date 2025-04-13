<?php

declare(strict_types=1);

namespace App\Patente\Models;

use App\Traits\SortableTrait;
use Carbon\Carbon;
use App\Nomadelfia\Persona\Models\Persona;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $stato
 * @property int $persona_id
 * @property string $data_rilascio_patente
 * @property string $rilasciata_dal
 * @property string $data_scadenza_patente
 * @property string $note
 */
final class Patente extends Model
{
    use SortableTrait;

    public $increment = false;

    public $keyType = 'string';

    public $timestamps = false;

    protected $connection = 'db_patente';

    protected $table = 'persone_patenti';

    protected $primaryKey = 'numero_patente';

    protected $guarded = [];

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id', 'id');
    }

    public function categorie(): BelongsToMany
    {
        return $this->belongsToMany(CategoriaPatente::class, 'patenti_categorie', 'numero_patente', 'categoria_patente_id');
    }

    public function cqc(): BelongsToMany
    {
        return $this->belongsToMany(CQC::class, 'patenti_categorie', 'numero_patente', 'categoria_patente_id')
            ->withPivot('data_rilascio', 'data_scadenza');
    }

    public function cqcPersone()
    {
        return $this->cqc()->wherePivot('categoria_patente_id', 16)->first();
    }

    public function cqcMerci()
    {
        return $this->cqc()->wherePivot('categoria_patente_id', 17)->first();
    }

    public function categorieAsString()
    {
        return $this->categorie()->get()->implode('categoria', ',');
    }

    public function scopeInScadenza($query, int $days)
    {
        $data = Carbon::now()->addDays($days)->toDateString();

        return $query->where('data_scadenza_patente', '<=', $data)
            ->where('data_scadenza_patente', '>=', Carbon::now()->toDateString());

    }

    public function scopeNonScadute($query)
    {
        $data = Carbon::now()->toDateString();

        return $query->where('data_scadenza_patente', '>', $data);
    }

    public function scopeScadute($query, ?int $days = null)
    {
        if ($days !== null) {
            $data = Carbon::now()->subDays($days)->toDateString();

            return $query->where('data_scadenza_patente', '>=', $data)
                ->where('data_scadenza_patente', '<=', Carbon::now()->toDateString());
        }

        return $query->where('data_scadenza_patente', '<=', Carbon::now()->toDateString());

    }

    public function hasCommissione(): bool
    {
        return $this->stato === 'commissione';
    }

    public function scopeConCommisione($query)
    {
        return $query->where('stato', '=', 'commissione');
    }

    public function scopeSenzaCommisione($query)
    {
        return $query->whereNull('stato')
            ->orWhere('stato', '!=', 'commissione');
    }

    protected static function booted(): void
    {
        self::addGlobalScope('InNomadelfia', function (Builder $builder): void {
            $builder->select('db_nomadelfia.persone.*', 'persone_patenti.*')
                ->join('db_nomadelfia.popolazione', 'db_nomadelfia.popolazione.persona_id', '=', 'persone_patenti.persona_id')
                ->join('db_nomadelfia.persone', 'db_nomadelfia.persone.id', '=', 'persone_patenti.persona_id')
                ->whereNull('db_nomadelfia.popolazione.data_uscita')
                ->orderBy('db_nomadelfia.persone.nominativo');
        });
    }
}
