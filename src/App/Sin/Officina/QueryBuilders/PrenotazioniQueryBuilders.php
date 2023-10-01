<?php

namespace App\Officina\QueryBuilders;

use App\Officina\Models\Prenotazioni;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PrenotazioniQueryBuilders extends Builder
{
    public function ActiveYesterday()
    {
        $yesterday = Carbon::now()->subDay()->toDateString();

        return $this->ActiveIn($yesterday, '00:00', $yesterday, '23:59');
    }

    public function ActiveToday()
    {
        $today = Carbon::now()->toDateString();

        return $this->ActiveIn($today, '00:00', $today, '23:59'); // ->orWhere('data_arrivo', "=", $today);
    }

    public function ActiveIn($data_from, $ora_from, $data_to, $ora_to)
    {

        $prenotazioni = Prenotazioni::where('data_partenza', '=', $data_from)
            // prenotazioni attive nello stesso giorno guardando l'ora
            ->where('data_arrivo', '=', $data_to)
            ->where(function ($query) use ($ora_from, $ora_to) {
                $query->where([['ora_partenza', '<', $ora_to], ['ora_arrivo', '>', $ora_from]]);
            })
            ->orWhere(function ($query) use ($data_to, $ora_from) {
                // prenotazione che partono nei giorni precedenti e finiscono il giorno dell partenza
                // con ora di arrivo è maggiore dellora di inizio prenotazione
                $query->where('data_arrivo', '=', $data_to)
                    ->where('data_partenza', '!=', $data_to) // elimina partenza nello stesso giorno
                    ->where('ora_arrivo', '>', $ora_from);
            });

        //            ->orWhere(function($query) use ($data_to, $data_from) {
        //                $query->where('data_partenza', '<', $data_to)
        //                    ->where('data_arrivo', '>', $data_from);
        //            });
        return $prenotazioni;
    }
}
