<?php

namespace App\Officina\QueryBuilders;

use App\Officina\Models\Prenotazioni;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PrenotazioniQueryBuilders extends Builder
{
    public function yesterday(): Builder
    {
        $startOfDay = Carbon::yesterday()->startOfDay();
        $endOfDay = Carbon::yesterday()->endOfDay();

        return $this->activeIn($startOfDay, $endOfDay);
    }

    public function today(): Builder
    {
        $startOfDay = Carbon::today()->startOfDay();
        $endOfDay = Carbon::today()->endOfDay();

        return $this->activeIn($startOfDay, $endOfDay);
    }

    public function activeIn(Carbon $data_from, Carbon $data_to): Builder
    {
        // prenotazioni attive nello stesso giorno guardando l'ora
        $prenotazioni = Prenotazioni::where('data_partenza', '=', $data_from->toDateString())
            ->where('data_arrivo', '=', $data_to->toDateString())
            ->where(function ($query) use ($data_from, $data_to) {
                $query->where([['ora_partenza', '<', $data_to->format('H:i')], ['ora_arrivo', '>', $data_from->format('H:i')]]);
            })
            ->orWhere(function ($query) use ($data_to, $data_from) {
                // prenotazione che partono nei giorni precedenti e finiscono il giorno della partenza
                // con ora di arrivo maggiore dell' ora di inizio prenotazione
                $query->where('data_arrivo', '=', $data_to->toDateString())
                    ->where('data_partenza', '!=', $data_to->toDateString()) // elimina partenza nello stesso giorno
                    ->where('ora_arrivo', '>', $data_from->format('H:i'));
            });

        //            ->orWhere(function($query) use ($data_to, $data_from) {
        //                $query->where('data_partenza', '<', $data_to)
        //                    ->where('data_arrivo', '>', $data_from);
        //            });
        return $prenotazioni;
    }

    public function legacyAttiveIn($datap, $orap, $dataa, $oraa): \Illuminate\Support\Collection
    {
        if ($datap == null && $orap == null && $dataa == null && $oraa == null) {
            return collect();
        }

        // lista delle prenotazioni attive tra l'ora di partenza e l'ora di arrivo
        $IDPrenotazioniAttive = collect();

        //prenotazioni attive guardando solo le date: datapartenza e dataarrivo
        $IDPrenotazioniAttiveData = Prenotazioni::with(['cliente'])
            ->where('data_partenza', '<', $dataa)
            ->where('data_arrivo', '>', $datap)
            ->pluck('id');

        // prenotazioni attive guardando le date e le ore
        $IDPrenotazioniAttiveDataOra = Prenotazioni::with(['cliente'])
            ->where('data_arrivo', '=', $datap)
            ->where('data_partenza', '!=', $datap) // elimina partenza nello stesso giorno
            ->where('ora_arrivo', '>', $orap)
            ->pluck('id');

        //prenotazioni attive guardando le date e ore
        $IDPrenotazioniAttiveDataOra2 = Prenotazioni::with(['cliente'])
            ->where('data_partenza', '=', $dataa)
            ->where('data_arrivo', '!=', $dataa) // elimina partenza nello stesso giorno
            ->where('ora_partenza', '<', $oraa)
            ->pluck('id');

        // prenotazioni attive nello stesso giorno guardando l'ora
        $IDPrenotazioniAttiveOggi = Prenotazioni::with(['cliente'])
            ->where('data_partenza', '=', $datap)
            ->where('data_arrivo', '=', $dataa)
            ->where(function ($query) use ($orap, $oraa) {
                // esclude le prenotazioni che sono a cavallo dell'ora di partenza della nuova prenotazione
                $query->where([['ora_partenza', '<', $oraa], ['ora_arrivo', '>', $orap]]);
            })
            ->pluck('id');

        $IDPrenotazioniAttive = collect();
        $IDPrenotazioniAttive = $IDPrenotazioniAttive->merge($IDPrenotazioniAttiveData);
        $IDPrenotazioniAttive = $IDPrenotazioniAttive->merge($IDPrenotazioniAttiveOggi);
        $IDPrenotazioniAttive = $IDPrenotazioniAttive->merge($IDPrenotazioniAttiveDataOra2);
        $IDPrenotazioniAttive = $IDPrenotazioniAttive->merge($IDPrenotazioniAttiveDataOra);

        return $IDPrenotazioniAttive;
    }
}
