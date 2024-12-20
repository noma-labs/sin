<?php

declare(strict_types=1);

namespace App\Patente\Models;

use App\Traits\SortableTrait;
use Carbon;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $categoria
 *
 * @method static scadute($days = null)
 */
final class CQC extends Model
{
    use SortableTrait;

    public $timestamps = false;

    protected $connection = 'db_patente';

    protected $table = 'categorie';

    protected $guarded = [];

    public function patenti(): BelongsToMany
    {
        return $this->belongsToMany(Patente::class, 'patenti_categorie', 'categoria_patente_id', 'numero_patente')
            ->withPivot('data_rilascio', 'data_scadenza');
    }

    /**
     * Get all of the persona that belong to the CQC.
     */
    public function persona(): BelongsToMany
    {
        return $this->persona()->using(Patente::class);
    }

    /**
     * Ritorna il C.Q:C persone
     *
     * @author Davide Neri
     */
    public function scopeCQCPersone($query): self
    {
        return $query->where('id', 16)->first();
    }

    /**
     * Ritorna la categorie del C.Q:C merci
     *
     * @author Davide Neri
     */
    public function scopeCQCMerci($query): self
    {
        return $query->where('id', 17)->first();
    }

    /**
     * Ritorna le patenti che scadono entro $days giorni
     *
     * @param  int  $days  :numero di giorni entro il quale le patenti scadono.
     *
     * @author Davide Neri
     */
    public function scopeinScadenza($query, $days): BelongsToMany
    {
        $data = Carbon::now()->addDays($days)->toDateString();

        return $this->belongsToMany(Patente::class, 'patenti_categorie', 'categoria_patente_id', 'numero_patente')
            ->withPivot('data_rilascio', 'data_scadenza')
            ->wherePivot('data_scadenza', '<=', $data)
            ->wherePivot('data_scadenza', '>=', Carbon::now()->toDateString());
    }

    /**
     * Ritorna le patenti con C.Q.C che non sono in scadenza da $days giorni in poi.
     *
     * @param  int  $days  : numero di giorni entro il quale le patenti scadono.
     *
     * @author Davide Neri
     */
    public function scopeNonInScadenza($query, int $days): BelongsToMany
    {
        $data = Carbon::now()->addDays($days)->toDateString();

        return $this->belongsToMany(Patente::class, 'patenti_categorie', 'categoria_patente_id', 'numero_patente')
            ->withPivot('data_rilascio', 'data_scadenza')
            ->wherePivot('data_scadenza', '>', $data);

    }

    /**
     * Ritorna le patenti con C.Q.C scadeute.
     * Se $days è null ritorna tutte le patenti scadute, altimenti solo quelle scadute d $days giorni.
     *
     * @param  int  $days  : numero di giorni | null
     *
     * @author Davide Neri
     */
    public function scadute($days = null): BelongsToMany
    {
        if ($days !== null) {
            $data = Carbon::now()->subDays($days)->toDateString();

            return $this->belongsToMany(Patente::class, 'patenti_categorie', 'categoria_patente_id', 'numero_patente')
                ->withPivot('data_rilascio', 'data_scadenza')
                ->wherePivot('data_scadenza', '>=', $data)
                ->wherePivot('data_scadenza', '<=', Carbon::now()->toDateString());
        }

        return $this->belongsToMany(Patente::class, 'patenti_categorie', 'categoria_patente_id', 'numero_patente')
            ->withPivot('data_rilascio', 'data_scadenza')
            ->wherePivot('data_scadenza', '<=', Carbon::now()->toDateString());

    }

    /**
     * Define a global scope for obtaining only the CQC among all the actegorie.
     */
    protected static function boot(): void
    {
        parent::boot();
        self::addGlobalScope('id', function (Builder $builder): void {
            $builder->where('id', 16)->orWhere('id', 17);
        });
    }
}
