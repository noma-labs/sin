<?php

declare(strict_types=1);

namespace App\Officina\Controllers;

use App\Officina\Models\Prenotazioni;
use App\Officina\Models\Uso;
use App\Officina\Models\Veicolo;
use App\Officina\Models\ViewClienti;
use App\Officina\Models\ViewMeccanici;
use Illuminate\Http\Request;

final class SearchableReservationsController
{
    public function search(Request $request)
    {
        $usi = Uso::all();
        $clienti = ViewClienti::orderBy('nominativo', 'asc')->get();
        $veicoli = Veicolo::orderBy('nome')->get();
        $meccanici = ViewMeccanici::orderBy('nominativo')->get();

        $prenotazioni = collect();
        $msgSearch = '';

        if ($request->except(['_token'])) {
            $queryPrenotazioni = Prenotazioni::where(function ($q) use ($request, &$msgSearch, &$orderBy): void {
                if ($request->filled('cliente_id')) {
                    $cliente = ViewClienti::findOrFail($request->input('cliente_id'));
                    $q->where('cliente_id', $cliente->id);
                    $msgSearch = $msgSearch.' Cliente='.$cliente->nominativo;
                }
                if ($request->filled('veicolo_id')) {
                    $veicolo = Veicolo::withTrashed()->findOrFail($request->input('veicolo_id'));
                    $q->where('veicolo_id', $veicolo->id);
                    $msgSearch = $msgSearch.' Veicolo='.$veicolo->nome;
                    $orderBy = 'titolo';
                }
                if ($request->filled('meccanico_id')) {
                    $meccanico = ViewMeccanici::findOrFail($request->input('meccanico_id'));
                    $q->where('meccanico_id', $meccanico->persona_id);
                    $msgSearch = $msgSearch.' Meccanico='.$meccanico->nominativo;
                    $orderBy = 'titolo';
                }

                if ($request->filled('uso_id')) {
                    $uso = Uso::findOrFail($request->input('uso_id'));
                    $q->where('uso_id', $uso->ofus_iden);
                    $msgSearch = $msgSearch.' Uso='.$uso->ofus_nome;
                }
                $cdp = $request->input('criterio_data_partenza', null);
                $cda = $request->input('criterio_data_arrivo', null);
                $dp = $request->input('data_partenza', null);
                $da = $request->input('data_arrivo', null);
                $ds = $request->input('data_singola', null);

                if ($ds) { // ricerca tutte le prenotazione che contengono in singolo giorno
                    $q->where('data_arrivo', '>=', $ds);
                    $q->where('data_partenza', '<=', $ds);
                    $msgSearch = $msgSearch." Data Partenza  <= $ds  <=  Data arrivo";
                } else {
                    if ($cdp and $dp) {
                        $q->where('data_partenza', $cdp, $dp);
                        $msgSearch = $msgSearch.' Data Partenza'.$cdp.$dp;
                    }
                    if ($cda and $da) {
                        $q->where('data_arrivo', $cda, $da);
                        $msgSearch = $msgSearch.' Data Partenza'.$cda.$da;
                    }
                }

                // if ($request->filled('criterio_data_partenza') and $request->filled('data_partenza') ) {
                //   $q->where('data_partenza', $request->input('criterio_data_partenza'), $request->input('data_partenza'));
                //   $msgSearch= $msgSearch." Data Partenza".$request->input('criterio_data_partenza').$request->input('data_partenza');
                // }
                // if ($request->filled('criterio_data_arrivo') and $request->filled('data_arrivo') ) {
                //   $q->where('data_arrivo', $request->input('criterio_data_arrivo'), $request->input('data_arrivo'));
                //   $msgSearch= $msgSearch." Data Partenza".$request->input('criterio_data_arrivo').$request->input('data_arrivo');
                // }
                if ($request->filled('note')) {
                    $q->where('note', 'LIKE', '%'.$request->note.'%');
                    $msgSearch = $msgSearch.' Note='.$request->note;
                }
            });

            $prenotazioni = $queryPrenotazioni->orderBy('data_partenza', 'desc')
                ->orderBy('data_arrivo', 'desc')
                ->orderBy('ora_partenza', 'desc')
                ->orderBy('ora_arrivo', 'asc')
                ->paginate(25);

        }

        return view('officina.prenotazioni.search', compact('clienti', 'veicoli', 'meccanici', 'usi', 'prenotazioni', 'msgSearch'));
    }
}
