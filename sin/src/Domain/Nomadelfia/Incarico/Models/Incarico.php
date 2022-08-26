<?php

namespace Domain\Nomadelfia\Incarico\Models;

use Database\Factories\IncaricoFactory;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Incarico extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $connection = 'db_nomadelfia';
    protected $table = 'incarichi';
    protected $primaryKey = "id";

    protected $guarded = [];

    protected static function newFactory()
    {
        return IncaricoFactory::new();
    }
    
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderby('nome');
        });
    }

    public function lavoratori()
    {
        return $this->belongsToMany(Persona::class, 'incarichi_persone', 'incarico_id', 'persona_id')
            ->withPivot('data_inizio')
            ->orderBy('persone.nominativo');
    }

    public function lavoratoriAttuali()
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

        $current = collect($this->lavoratoriAttuali);
        $ids = $current->map(function ($item) {
            return $item->id;
        });
        return $all->whereNotIn('id', $ids);
    }

    /**
     * Returns the people that have more than $minNUm incarichi.
     * @param int $minNum
     * @return \Illuminate\Support\Collection
     */
    public static function getBusyPeople(int $minNum = 3)
    {
        $personeCount = DB::connection('db_nomadelfia')
            ->table('incarichi_persone')
            ->selectRaw("persone.id, max(persone.nominativo) as nominativo,  count(*)  as count")
            ->leftJoin('persone', 'persone.id', '=', 'incarichi_persone.persona_id')
            ->whereNull("incarichi_persone.data_fine")
            ->groupBy("persone.id")
            ->having("count", ">=", $minNum)
            ->orderBy("count", "DESC")
            //->limit($limit)
            //->limit($limit)
            ->get();
        return $personeCount;
    }


}
