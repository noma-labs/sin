<?php

namespace App\Patente\Models;

use Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $categoria
 */
class CategoriaPatente extends Model
{
    protected $connection = 'db_patente';

    protected $table = 'categorie';

    protected $hidden = ['pivot']; // do not return the pivot with the "data_rilascio" e "data_scadenza" (se decommentato controllare component vue)

    public $timestamps = false;

    protected $guarded = [];

    // Remove CQC (persone e merci) from the categories
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('id', function (Builder $builder): void {
            $builder->where('id', '!=', 16)->Where('id', '!=', 17);
        });
    }

    /**
     * Scope a query to only include categorie of a given name (A,B,C,D,DE,...)
     *
     * @param  Builder  $query
     * @param  string  $categoria
     * @return Builder
     */
    public function scopeDalNome($query, $categoria)
    {
        return $query->where('categoria', $categoria)->first();
    }

    public function patenti(): BelongsToMany
    {
        return $this->belongsToMany(Patente::class, 'patenti_categorie', 'categoria_patente_id', 'numero_patente');
    }

    /**
     * Ritorna la categorie del C.Q:C persone
     *
     * @author Davide Neri
     */
    public function scopeCQCPersone($query)
    {
        return $query->where('id', 16)->first();
    }

    /**
     * Ritorna la categorie del C.Q:C merci
     *
     * @author Davide Neri
     */
    public function scopeCQCMerci($query)
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
    public function inScadenza($days)
    {
        $data = Carbon::now()->addDays($days)->toDateString();

        return $this->belongsToMany(Patente::class, 'patenti_categorie', 'categoria_patente_id', 'numero_patente')
            ->wherePivot('data_scadenza', '<=', $data)
            ->wherePivot('data_scadenza', '>=', Carbon::now()->toDateString());
    }

    public function scadute($days)
    {
        $data = Carbon::now()->subDays($days)->toDateString();

        return $this->belongsToMany(Patente::class, 'patenti_categorie', 'categoria_patente_id', 'numero_patente')
            ->wherePivot('data_scadenza', '>=', $data)
            ->wherePivot('data_scadenza', '<=', Carbon::now()->toDateString());
    }
}
