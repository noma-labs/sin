<?php

namespace App\Officina\Models;

use Carbon\Carbon;
use Database\Factories\VeicoloFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property string $nome
 * @property string $targa
 * @property int $prenotabile
 */
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

    /**
     * Get all vehicles with bookings within a specified date range.
     *
     * @param  Carbon  $data_from The start date of the date range.
     * @param  Carbon  $data_to The end date of the date range.
     * @return Builder The query builder instance.
     */
    public static function withBookingsIn(Carbon $data_from, Carbon $data_to): Builder
    {
        return DB::connection('db_officina')
            ->table('veicolo')
            ->selectRaw('veicolo.id, veicolo.nome, db_nomadelfia.persone.nominativo, impiego.nome as impiego_nome , tipologia.nome as tipologia_nome, prenotazioni.id as prenotazione_id, concat(prenotazioni.data_partenza, ":",  prenotazioni.ora_partenza) as partenza, concat(prenotazioni.data_arrivo, ":", prenotazioni.ora_arrivo) as arrivo')
            ->leftJoin('prenotazioni', 'prenotazioni.veicolo_id', '=', 'veicolo.id')
            ->leftJoin('db_nomadelfia.persone', 'prenotazioni.cliente_id', '=', 'persone.id')
            ->leftJoin('impiego', 'impiego.id', '=', 'veicolo.impiego_id')
            ->leftJoin('tipologia', 'tipologia.id', '=', 'veicolo.tipologia_id')
            ->where('data_partenza', '=', $data_from->toDateString())->where('data_arrivo', '=', $data_to->toDateString())
            ->where(function ($query) use ($data_from, $data_to) {
                $query->where([['ora_partenza', '<', $data_to->format('H:i')], ['ora_arrivo', '>', $data_from->format('H:i')]]);
            })
            ->orWhere(function ($query) use ($data_to, $data_from) {
                // prenotazione che partono nei giorni precedenti e finiscono il giorno della partenza
                // con ora di arrivo maggiore dell' ora di inizio prenotazione
                $query->where('data_arrivo', '=', $data_to->toDateString())
                    ->where('data_partenza', '!=', $data_to->toDateString()) // elimina partenza nello stesso giorno
                    ->where('ora_arrivo', '>', $data_from->format('H:i'));
            })
            ->orWhere(function ($query) use ($data_to) {
                $query->where('data_partenza', '=', $data_to->toDateString())
                    ->where('data_arrivo', '!=', $data_to->toDateString()) // elimina partenza nello stesso giorno
                    ->where('ora_partenza', '<', $data_to->format('H:i'));
            })
            //prenotazioni attive guardando solo le date: datapartenza e dataarrivo
            ->orWhere(function ($query) use ($data_from, $data_to) {
                $query->where('data_partenza', '<', $data_to->toDateString())
                    ->where('data_arrivo', '>', $data_from->toDateString());
            })
            ->where('veicolo.prenotabile', 1)
            ->orWhereNull('prenotazioni.id'); // add also veicoli without a booking
    }

    public function impieghi(): BelongsTo
    {
        return $this->belongsTo('App\Officina\Models\Impiego');
    }

    public function modello(): HasOne
    {
        return $this->hasOne(Modelli::class, 'id', 'modello_id');
    }

    public function impiego(): HasOne
    {
        return $this->hasOne(Impiego::class, 'id', 'impiego_id');
    }

    public function tipologia(): HasOne
    {
        return $this->hasOne(Tipologia::class, 'id', 'tipologia_id');
    }

    public function alimentazione(): HasOne
    {
        return $this->hasOne(Alimentazioni::class, 'id', 'alimentazione_id');
    }

    public function prenotazioni(): HasMany
    {
        return $this->hasMany(Prenotazioni::class, 'veicolo_id');
    }

    // codice del filtro dell'aria del veicolo
    public function filtroAria(): HasOne
    {
        return $this->hasOne(TipoFiltro::class, 'id', 'filtro_aria');
    }

    // codice del filtro del gasolio del veicolo
    public function filtroGasolio(): HasOne
    {
        return $this->hasOne(TipoFiltro::class, 'id', 'filtro_gasolio');
    }

    // codice del filtro dell'olio del veicolo
    public function filtroOlio(): HasOne
    {
        return $this->hasOne(TipoFiltro::class, 'id', 'filtro_olio');
    }

    // codice del filtro dell'aria condizionata del veicolo
    public function filtroAriaCondizionata(): HasOne
    {
        return $this->hasOne(TipoFiltro::class, 'id', 'filtro_aria_condizionata');
    }

    // codice del tipo di olio motore del veicolo
    public function olioMotore(): HasOne
    {
        return $this->hasOne(TipoOlio::class, 'id', 'olio_id');
    }

    /**
     * ritorna tutti le gomme del veicolo
     */
    public function gomme(): BelongsToMany
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
}
