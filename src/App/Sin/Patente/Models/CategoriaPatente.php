<?php

declare(strict_types=1);

namespace App\Patente\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $categoria
 */
final class CategoriaPatente extends Model
{
    public $timestamps = false;

    protected $connection = 'db_patente';

    protected $table = 'categorie';

    protected $hidden = ['pivot']; // do not return the pivot with the "data_rilascio" e "data_scadenza" (se decommentato controllare component vue)

    protected $guarded = [];

    public function patenti(): BelongsToMany
    {
        return $this->belongsToMany(Patente::class, 'patenti_categorie', 'categoria_patente_id', 'numero_patente');
    }

    public function scopeCQCPersone($query)
    {
        return $query->where('id', 16)->first();
    }

    public function scopeCQCMerci($query)
    {
        return $query->where('id', 17)->first();
    }

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

    protected static function boot(): void
    {
        parent::boot();
        self::addGlobalScope('id', function (Builder $builder): void {
            $builder->where('id', '!=', 16)->Where('id', '!=', 17);
        });
    }
}
