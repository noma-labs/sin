<?php

namespace Domain\Nomadelfia\Azienda\Models;

use Domain\Nomadelfia\Persona\Models\Persona;
use App\Traits\Enums;
use Database\Factories\AziendaFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Azienda extends Model
{
    use Enums;
    use HasFactory;

    public $timestamps = true;

    protected $connection = 'db_nomadelfia';
    protected $table = 'aziende';
    protected $primaryKey = "id";

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderby('nome_azienda');
        });
    }

    protected static function newFactory()
    {
        return AziendaFactory::new();
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
            'data_inizio_azienda')->orderBy('mansione', 'asc')->orderBy('persone.nominativo');
    }

    public function lavoratoriAttuali()
    {
        return $this->lavoratori()->wherePivotIn('stato', ['Attivo', 'Sospeso'])->withPivot('data_inizio_azienda',
            'mansione',
            'stato');
    }

    public function lavoratoriStorici()
    {
        return $this->lavoratori()->wherePivot('stato',
            '=', 'Non Attivo')->withPivot('data_fine_azienda', 'stato');
    }

    public static function scuola()
    {
        return static::perNome("scuola");
    }

    public static function perNome($nome)
    {
        return static::where('nome_azienda', $nome)->first();
    }

    public function isIncarico()
    {
        return $this->tipo == 'incarico';
    }

    public function isAzienda()
    {
        return $this->tipo == 'azienda';
    }


}