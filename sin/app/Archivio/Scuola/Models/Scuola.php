<?php

namespace App\Nomadelfia\Models;

use Illuminate\Database\Eloquent\Model;

use App\Nomadelfia\Models\Persona;
use App\Traits\Enums;
use Illuminate\Database\Eloquent\Builder;

class Scuola extends Model
{
    use Enums;

    public $timestamps = true;

    protected $connection = 'db_nomadelfia';
    protected $table = 'aziende';
    protected $primaryKey = "id";

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderby('nome_azienda');
        });
    }

    public function scopeAziende($query)
    {
        return $query->where('tipo', "=", 'azienda');
    }

    public function scopeIncarichi($query)
    {
        return $query->where('tipo', "=", 'incarico');
    }

    public function lavoratori()
    {
        return $this->belongsToMany(Persona::class, 'aziende_persone', 'azienda_id', 'persona_id')->withPivot('stato',
            'data_inizio_azienda');
    }

    public function lavoratoriAttuali()
    {
        return $this->belongsToMany(Persona::class, 'aziende_persone', 'azienda_id',
            'persona_id')->wherePivotIn('stato', ['Attivo', 'Sospeso'])->withPivot('data_inizio_azienda', 'mansione',
            'stato')->orderBy('mansione', 'asc');
    }

    public function lavoratoriStorici()
    {
        return $this->belongsToMany(Persona::class, 'aziende_persone', 'azienda_id', 'persona_id')->wherePivot('stato',
            '=', 'Non Attivo')->withPivot('data_fine_azienda', 'stato');
    }

    public static function perNome($nome)
    {
        return static::where('nome_azienda', $nome)->first();
    }


}
