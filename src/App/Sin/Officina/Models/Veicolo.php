<?php

namespace App\Officina\Models;

use Database\Factories\VeicoloFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Veicolo extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'veicolo';

    protected $connection = 'db_officina';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public $timestamps = false;

    protected static function newFactory()
    {
        return VeicoloFactory::new();
    }

    public function impieghi()
    {
        return $this->belongsTo('App\Officina\Models\Impiego');
    }

    public function modello()
    {
        return $this->hasOne(Modelli::class, 'id', 'modello_id');
    }

    public function impiego()
    {
        return $this->hasOne(Impiego::class, 'id', 'impiego_id');
    }

    public function tipologia()
    {
        return $this->hasOne(Tipologia::class, 'id', 'tipologia_id');
    }

    public function alimentazione()
    {
        return $this->hasOne(Alimentazioni::class, 'id', 'alimentazione_id');
    }

    public function prenotazioni()
    {
        return $this->hasMany(Prenotazioni::class, 'veicolo_id');
    }

    // codice del filtro dell'aria del veicolo
    public function filtroAria()
    {
        return $this->hasOne(TipoFiltro::class, 'id', 'filtro_aria');
    }

    // codice del filtro del gasolio del veicolo
    public function filtroGasolio()
    {
        return $this->hasOne(TipoFiltro::class, 'id', 'filtro_gasolio');
    }

    // codice del filtro dell'olio del veicolo
    public function filtroOlio()
    {
        return $this->hasOne(TipoFiltro::class, 'id', 'filtro_olio');
    }

    // codice del filtro dell'aria condizionata del veicolo
    public function filtroAriaCondizionata()
    {
        return $this->hasOne(TipoFiltro::class, 'id', 'filtro_aria_condizionata');
    }

    // codice del tipo di olio motore del veicolo
    public function olioMotore()
    {
        return $this->hasOne(TipoOlio::class, 'id', 'olio_id');
    }

    /**
     * ritorna tutti le gomme del veicolo
     */
    public function gomme()
    {
        return $this->belongsToMany(TipoGomme::class, 'gomme_veicolo', 'veicolo_id', 'gomme_id');
    }

    public function scopePrenotabili($query)
    {
        return $query->where('prenotabile', true);
    }

    //IMPIEGO
    public function scopeInterni($query)
    {
        return $query->where('impiego_id', 1);
    }

    public function scopeGrosseto($query)
    {
        return $query->where('impiego_id', 2);
    }

    public function scopeViaggiLunghi($query)
    {
        return $query->where('impiego_id', 3);
    }

    public function scopePersonali($query)
    {
        return $query->where('impiego_id', 4);
    }

    public function scopeRoma($query)
    {
        return $query->where('impiego_id', 5);
    }

    // TIPOLOGIA
    public function scopeAutovettura($query)
    {
        return $query->where('tipologia_id', 1);
    }

    public function scopeAutocarri($query)
    {
        return $query->where('tipologia_id', 2);
    }

    public function scopeFurgoni($query)
    {
        return $query->where('tipologia_id', 7);
    }

    public function scopePulmino($query)
    {
        return $query->where('tipologia_id', 6);
    }

    public function scopeAutobus($query)
    {
        return $query->where('tipologia_id', 3);
    }

    public function scopeMotocicli($query)
    {
        return $query->where('tipologia_id', 10);
    }

    /**
     * cambia la classe di media_model
     */
    public function media()
    {
        return $this->morphMany(Documento::class, 'model');
    }
}
