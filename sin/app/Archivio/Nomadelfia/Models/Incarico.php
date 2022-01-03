<?php

namespace App\Nomadelfia\Models;

use App\Traits\Enums;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Incarico extends Model
{

    public $timestamps = true;

    protected $connection = 'db_nomadelfia';
    protected $table = 'incarichi';
    protected $primaryKey = "id";

    protected $guarded = [];

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
        return $this->lavoratori()->wherePivot('data_fine', "IS NOT", null)->withPivot('data_fine');
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


}
