<?php

namespace Domain\Nomadelfia\Incarico\Models;

use Database\Factories\IncaricoFactory;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * @property string $descrizione
 * @property string $nome
 */
class Incarico extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $connection = 'db_nomadelfia';

    protected $table = 'incarichi';

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected static function newFactory()
    {
        return IncaricoFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder): void {
            $builder->orderby('nome');
        });
    }

    public function lavoratori(): BelongsToMany
    {
        return $this->belongsToMany(Persona::class, 'incarichi_persone', 'incarico_id', 'persona_id')
            ->withPivot('data_inizio')
            ->orderBy('persone.nominativo');
    }

    public function lavoratoriAttuali(): BelongsToMany
    {
        return $this->lavoratori()->wherePivot('data_fine', null)->withPivot('data_inizio');
    }

    public function lavoratoriStorici()
    {
        return $this->lavoratori()->wherePivot('data_fine', '!=', 'null')->withPivot('data_fine');
    }

    public function lavoratoriPossibili()
    {
        $all = PopolazioneNomadelfia::daEta(18);

        $ids = $this->lavoratoriAttuali()->get()->pluck('id');

        return $all->whereNotIn('id', $ids);
    }

    /**
     * Returns the people that have more than $minNUm incarichi.
     *
     * @return Collection
     */
    public static function getBusyPeople(int $minNum = 3)
    {
        return DB::connection('db_nomadelfia')
            ->table('incarichi_persone')
            ->selectRaw('persone.id, max(persone.nominativo) as nominativo,  count(*)  as count')
            ->leftJoin('persone', 'persone.id', '=', 'incarichi_persone.persona_id')
            ->whereNull('incarichi_persone.data_fine')
            ->groupBy('persone.id')
            ->having('count', '>=', $minNum)
            ->orderBy('count', 'DESC')
            //->limit($limit)
            //->limit($limit)
            ->get();
    }
}
