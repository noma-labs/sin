<?php

declare(strict_types=1);

namespace App\Patente\Models;

use Carbon\Carbon;
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
    public $timestamps = false;

    protected $connection = 'db_patente';

    protected $table = 'categorie';

    protected $guarded = [];

    public function patenti(): BelongsToMany
    {
        return $this->belongsToMany(Patente::class, 'patenti_categorie', 'categoria_patente_id', 'numero_patente')
            ->withPivot('data_rilascio', 'data_scadenza');
    }

    public function persona(): BelongsToMany
    {
        return $this->persona()->using(Patente::class);
    }

    protected function scopeCQCPersone($query): self
    {
        return $query->where('id', 16)->first();
    }

    protected function scopeCQCMerci($query): self
    {
        return $query->where('id', 17)->first();
    }

    protected function scopeinScadenza($query, $days): BelongsToMany
    {
        $data = \Illuminate\Support\Facades\Date::now()->addDays($days)->toDateString();

        return $this->belongsToMany(Patente::class, 'patenti_categorie', 'categoria_patente_id', 'numero_patente')
            ->withPivot('data_rilascio', 'data_scadenza')
            ->wherePivot('data_scadenza', '<=', $data)
            ->wherePivot('data_scadenza', '>=', \Illuminate\Support\Facades\Date::now()->toDateString());
    }

    protected function scopeNonInScadenza($query, int $days): BelongsToMany
    {
        $data = \Illuminate\Support\Facades\Date::now()->addDays($days)->toDateString();

        return $this->belongsToMany(Patente::class, 'patenti_categorie', 'categoria_patente_id', 'numero_patente')
            ->withPivot('data_rilascio', 'data_scadenza')
            ->wherePivot('data_scadenza', '>', $data);

    }

    public function scadute($days = null): BelongsToMany
    {
        if ($days !== null) {
            $data = \Illuminate\Support\Facades\Date::now()->subDays($days)->toDateString();

            return $this->belongsToMany(Patente::class, 'patenti_categorie', 'categoria_patente_id', 'numero_patente')
                ->withPivot('data_rilascio', 'data_scadenza')
                ->wherePivot('data_scadenza', '>=', $data)
                ->wherePivot('data_scadenza', '<=', \Illuminate\Support\Facades\Date::now()->toDateString());
        }

        return $this->belongsToMany(Patente::class, 'patenti_categorie', 'categoria_patente_id', 'numero_patente')
            ->withPivot('data_rilascio', 'data_scadenza')
            ->wherePivot('data_scadenza', '<=', \Illuminate\Support\Facades\Date::now()->toDateString());

    }

    protected static function boot(): void
    {
        parent::boot();
        self::addGlobalScope('id', function (Builder $builder): void {
            $builder->where('id', 16)->orWhere('id', 17);
        });
    }
}
