<?php

namespace App\Officina\Models;

use App\Officina\QueryBuilders\PrenotazioniQueryBuilders;
use App\Traits\SortableTrait;
use Carbon\Carbon;
use Database\Factories\PrenotazioniFactory;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $data_partenza
 * @property string $ora_partenza
 * @property string $data_arrivo
 * @property string $ora_arrivo
 */
class Prenotazioni extends Model
{
    use HasFactory;
    use SoftDeletes;
    use SortableTrait;

    protected $table = 'prenotazioni';

    protected $connection = 'db_officina';

    protected $primareKey = 'id';

    protected $guarded = [];

    protected $dates = ['deleted_at'];

    public function newEloquentBuilder($query): PrenotazioniQueryBuilders
    {
        return new PrenotazioniQueryBuilders($query);
    }

    protected static function newFactory(): PrenotazioniFactory
    {
        return PrenotazioniFactory::new();
    }

    public function uso()
    {
        return $this->hasOne(Uso::class, 'ofus_iden', 'uso_id');
    }

    public function meccanico(): HasOne
    {
        return $this->hasOne(Persona::class, 'id', 'meccanico_id');
    }

    public function cliente(): HasOne
    {
        return $this->hasOne(Persona::class, 'id', 'cliente_id');
    }

    public function veicolo(): HasOne
    {
        return $this->hasOne(Veicolo::class, 'id', 'veicolo_id')->withTrashed();
    }

    /**
     * ritorna la data e l'ora di partenza
     *
     * @return Carbon
     */
    public function dataOraPartenza()
    {
        return Carbon::createFromFormat('Y-m-d H:i', $this->data_partenza.' '.$this->ora_partenza);
    }

    /**
     * ritorna la data e l'ora di arrivo
     *
     * @return Carbon
     */
    public function dataOraArrivo()
    {
        return Carbon::createFromFormat('Y-m-d H:i', $this->data_arrivo.' '.$this->ora_arrivo);
    }

    /**
     * ritorna true se la macchina è partita e non ancora arrivata
     *
     * @return bool
     */
    public function isPartita()
    {
        $adesso = Carbon::now();
        if ($this->dataOraArrivo() >= $adesso && $this->dataOraPartenza() <= $adesso) {
            return true;
        }

        return false;
    }

    /**
     * ritorna true se la macchina è arrivata
     */
    public function isArrivata()
    {
        if ($this->dataOraArrivo() < Carbon::now()) {
            return true;
        }

        return false;
    }

    /**
     * ritorna true se la macchina deve ancora partire
     */
    public function deveAncoraPartire()
    {
        if ($this->dataOraPartenza() > Carbon::now()) {
            return true;
        }

        return false;
    }
}
