<?php

declare(strict_types=1);

namespace App\Officina\Models;

use App\Traits\SortableTrait;
use Carbon\Carbon;
use Database\Factories\PrenotazioniFactory;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * @property string $data_partenza
 * @property string $ora_partenza
 * @property string $data_arrivo
 * @property string $ora_arrivo
 */
final class Prenotazioni extends Model
{
    use HasFactory;
    use SoftDeletes;
    use SortableTrait;

    protected $table = 'prenotazioni';

    protected $connection = 'db_officina';

    protected $primareKey = 'id';

    protected $guarded = [];

    public static function inTimeRange(Carbon $data_from, Carbon $data_to): Builder
    {
        return DB::connection('db_officina')
            ->table('prenotazioni')
            ->select('*')
            ->whereNull('deleted_at')
            ->where(function ($query) use ($data_from, $data_to): void {
                $query->where('data_partenza', '=', $data_from->toDateString())
                    ->where('data_arrivo', '=', $data_to->toDateString())
                    ->where(function ($query) use ($data_from, $data_to): void {
                        $query->where([['ora_partenza', '<', $data_to->format('H:i')], ['ora_arrivo', '>', $data_from->format('H:i')]]);
                    })
                    ->orWhere(function ($query) use ($data_to, $data_from): void {
                        // prenotazione che partono nei giorni precedenti e finiscono il giorno della partenza
                        // con ora di arrivo maggiore dell' ora di inizio prenotazione
                        $query->where('data_arrivo', '=', $data_to->toDateString())
                            ->where('data_partenza', '!=', $data_to->toDateString()) // elimina partenza nello stesso giorno
                            ->where('ora_arrivo', '>', $data_from->format('H:i'));
                    })
                    ->orWhere(function ($query) use ($data_to): void {
                        $query->where('data_partenza', '=', $data_to->toDateString())
                            ->where('data_arrivo', '!=', $data_to->toDateString()) // elimina partenza nello stesso giorno
                            ->where('ora_partenza', '<', $data_to->format('H:i'));
                    })
                    // prenotazioni attive guardando solo le date: datapartenza e dataarrivo
                    ->orWhere(function ($query) use ($data_from, $data_to): void {
                        $query->where('data_partenza', '<', $data_to->toDateString())
                            ->where('data_arrivo', '>', $data_from->toDateString());
                    });
            });
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
     */
    public function dataOraPartenza(): ?Carbon
    {
        return Carbon::createFromFormat('Y-m-d H:i', $this->data_partenza.' '.$this->ora_partenza);
    }

    /**
     * ritorna la data e l'ora di arrivo
     */
    public function dataOraArrivo(): ?Carbon
    {
        return Carbon::createFromFormat('Y-m-d H:i', $this->data_arrivo.' '.$this->ora_arrivo);
    }

    /**
     * ritorna true se la macchina è partita e non ancora arrivata
     */
    public function isPartita(): bool
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
    public function isArrivata(): bool
    {
        if ($this->dataOraArrivo() < Carbon::now()) {
            return true;
        }

        return false;
    }

    /**
     * ritorna true se la macchina deve ancora partire
     */
    public function deveAncoraPartire(): bool
    {
        if ($this->dataOraPartenza() > Carbon::now()) {
            return true;
        }

        return false;
    }

    protected static function newFactory(): PrenotazioniFactory
    {
        return PrenotazioniFactory::new();
    }

    protected function casts(): array
    {
        return ['deleted_at' => 'datetime'];
    }
}
