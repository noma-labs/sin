<?php
namespace App\Officina\QueryBuilders;

use App\Officina\Models\Prenotazioni;
use Illuminate\Database\Eloquent\Builder;

class PrenotazioniQueryBuilders extends Builder
{

    public function AttiveIn($data_from, $ora_from, $data_to, $ora_to)
    {

        //prenotazioni attive guardando solo le date: data_topartenza e data_fromrrivo
        $prenotazioni = Prenotazioni::where('data_partenza', '<', $data_from)
            ->where('data_arrivo', '>', $data_to);
//            ->orWhere(function($query) use ($data_to, $ora_from){
//                //prenotazioni attive guardando le date e le ore
//                $query->where('data_arrivo', '=', $data_to)
//                    ->where('data_partenza', '!=', $data_to) // elimina partenza nello stesso giorno
//                    ->where('ora_arrivo', '>', $ora_from);
//            })
//            ->orWhere(function($query) use ($data_from, $ora_to){
//                //prenotazioni attive guardando le date e ore
//                $query->where('data_partenza', '=', $data_from)
//                    ->where('data_arrivo', '!=', $data_from) // elimina partenza nello stesso giorno
//                    ->where('ora_partenza', '<', $ora_to);
//            })
//            ->orWhere(function($query) use ($data_from, $ora_to){
//                //prenotazioni attive guardando le date e ore
//                $query->where('data_partenza', '=', $data_from)
//                    ->where('data_arrivo', '!=', $data_from) // elimina partenza nello stesso giorno
//                    ->where('ora_partenza', '<', $ora_to);
//            });


        // prenotazioni attive nello stesso giorno guardando l'ora
//        $IDPrenotazioniAttiveOggi = Prenotazioni::where('data_partenza', '=', $data_to)
//            ->where('data_arrivo', '=', $data_from)
//            ->where(function ($query) use ($ora_from, $ora_to) {
//                // esclude le prenotazioni che sono a cavallo dell'ora di partenza della nuova prenotazione
//                // $query->where([['ora_partenza', '<=', $ora_from],['ora_arrivo',">",$ora_from]])
//                //        // esclude le prenotazioni che sono a cavallo dell'ora di arrivo della nuova prenotazion
//                //       ->orWhere([['ora_partenza', '<', $ora_to],['ora_arrivo',">=",$ora_to]])
//                //        // esclude le prenotazioni che sono a all'interno dell'ora partenza e arrivo della nuova prenotazion
//                //       ->orWhere([['ora_partenza', '>=', $ora_from],['ora_arrivo',"<=",$ora_to]]);
//                $query->where([['ora_partenza', '<', $ora_to], ['ora_arrivo', '>', $ora_from]]);
//
//            })
//            ->pluck('id');

        return $prenotazioni;
    }

}