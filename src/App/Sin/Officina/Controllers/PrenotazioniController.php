<?php

namespace App\Officina\Controllers;

use App\Officina\Actions\CreatePrenotazioneAction;
use App\Officina\Models\Prenotazioni;
use App\Officina\Models\Uso as Uso;
use App\Officina\Models\Veicolo;
use App\Officina\Models\ViewClienti as ViewClienti;
use App\Officina\Models\ViewMeccanici;
use Carbon\Carbon;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Validator;

class PrenotazioniController
{
    public function searchView()
    {
        $usi = Uso::all();
        $clienti = ViewClienti::orderBy('nominativo', 'asc')->get();

        return view('officina.prenotazioni.search', ['clienti' => $clienti, 'usi' => $usi]);
    }

    public function search(Request $request)
    {
        $msgSearch = ' ';
        // $orderBy = "titolo";

        if (! $request->except(['_token'])) {
            return Redirect::back()->withError('Nessun criterio di ricerca selezionato oppure invalido');
        }

        $queryPrenotazioni = Prenotazioni::where(function ($q) use ($request, &$msgSearch, &$orderBy) {
            if ($request->filled('cliente_id')) {
                $cliente = ViewClienti::findOrFail($request->input('cliente_id'));
                $q->where('cliente_id', $cliente->id);
                $msgSearch = $msgSearch.' Cliente='.$cliente->nominativo;
                // $orderBy = "titolo";
            }
            if ($request->filled('veicolo_id')) {
                $veicolo = Veicolo::withTrashed()->findOrFail($request->input('veicolo_id'));
                $q->where('veicolo_id', $veicolo->id);
                $msgSearch = $msgSearch.' Veicolo='.$veicolo->nome;
                $orderBy = 'titolo';
            }
            if ($request->filled('meccanico_id')) {
                $meccanico = ViewMeccanici::findorFail($request->input('meccanico_id'));
                $q->where('meccanico_id', $meccanico->persona_id);
                $msgSearch = $msgSearch.' Meccanico='.$meccanico->nominativo;
                $orderBy = 'titolo';
            }

            if ($request->filled('uso_id')) {
                $uso = Uso::findOrFail($request->input('uso_id'));
                $q->where('uso_id', $uso->ofus_iden);
                $msgSearch = $msgSearch.' Uso='.$uso->ofus_nome;
                // $orderBy = "titolo";
            }
            $cdp = $request->input('criterio_data_partenza', null);
            $cda = $request->input('criterio_data_arrivo', null);
            $dp = $request->input('data_partenza', null);
            $da = $request->input('data_arrivo', null);
            $ds = $request->input('data_singola', null);

            if ($ds) { // ricerca tutte le prenotazione che contengono in singolo giorno
                $q->where('data_arrivo', '>=', $ds);
                $q->where('data_partenza', '<=', $ds);
                $msgSearch .= " Data Partenza  <= $ds  <=  Data arrivo";
            } else {
                if ($cdp && $dp) {
                    $q->where('data_partenza', $cdp, $dp);
                    $msgSearch = $msgSearch.' Data Partenza'.$cdp.$dp;
                }
                if ($cda && $da) {
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
            ->paginate(10);
        $usi = Uso::all();
        $clienti = ViewClienti::orderBy('nominativo', 'asc')->get();

        return view('officina.prenotazioni.search_results', ['clienti' => $clienti, 'usi' => $usi, 'prenotazioni' => $prenotazioni, 'msgSearch' => $msgSearch]);
    }

    public function prenotazioni(Request $request)
    {
        $day = $request->get('day', 'oggi');

        $clienti = ViewClienti::orderBy('nominativo', 'asc')->get();
        $usi = Uso::all();
        $meccanici = ViewMeccanici::orderBy('nominativo')->get();

        $query = null;
        $now = Carbon::now();
        // TODO: usare le PrenotazioneQueryBulders per prendere prenotazioni attive
        if ($day == 'oggi') {
            // $query = Prenotazioni::today();
            $query = Prenotazioni::where('data_partenza', '=', $now->toDateString())
                ->orWhere('data_arrivo', '=', $now->toDateString());
        } else {
            if ($day == 'ieri') {
                // $query = Prenotazioni::yesterday();
                $query = Prenotazioni::where('data_arrivo', '=', $now->subDay()->toDateString());
            }
            if ($day == 'all') {
                // include:
                //   1) prenotazioni che partono dopo oggi (o uguale)
                //   2) prenotazioni a cavallo di oggi
                //   3) prenotazioni che si concludono oggi
                $query = Prenotazioni::where('data_partenza', '>=', $now->toDateString())
                    ->orWhere(function ($query) use ($now) {
                        // prenotazioni a cavallo di oggi
                        $query->where('data_partenza', '<', $now->toDateString())
                            ->where('data_arrivo', '>', $now->toDateString());
                    })
                    ->orWhere('data_arrivo', '=', $now->toDateString());
            }
        }
        $prenotazioni = $query->orderBy('data_partenza', 'asc')
            ->with('meccanico', 'uso', 'veicolo', 'cliente')
            ->orderBy('data_arrivo', 'desc')
            ->orderBy('ora_partenza', 'desc')
            ->orderBy('ora_arrivo', 'asc')
            ->get();

        return view('officina.prenotazioni.index', ['clienti' => $clienti, 'usi' => $usi, 'meccanici' => $meccanici, 'prenotazioni' => $prenotazioni, 'day' => $day]);
    }

    public function prenotazioniSucc(Request $request)
    {
        $validRequest = Validator::make($request->all(), [
            'nome' => 'required',
            'veicolo' => 'required',
            'meccanico' => 'required',
            'data_par' => 'required|date',
            'ora_par' => 'required',
            'data_arr' => 'required|date|after_or_equal:data_par',
            'ora_arr' => 'required',
            'uso' => 'required',
            'destinazione' => 'required',
        ]);

        $validRequest->sometimes('ora_arr', 'after:ora_par', function ($input) {
            return $input->data_par == $input->data_arr;
        });

        if ($validRequest->fails()) {
            return redirect(route('officina.index'))->withErrors($validRequest)->withInput();
        }
        (new CreatePrenotazioneAction)(
            Persona::findOrFail($request->get('nome')),
            Veicolo::findOrFail($request->get('veicolo')),
            Persona::findOrFail($request->get('meccanico')),
            $request->get('data_par'),
            $request->get('data_arr'),
            $request->get('ora_par'),
            $request->get('ora_arr'),
            Uso::findOrFail($request->get('uso')),
            $request->get('note', ''),
            $request->input('destinazione', '')
        );

        return redirect(route('officina.prenota'))->withSuccess('Prenotazione eseguita.');

    }

    public function delete($id)
    {
        $pren = Prenotazioni::find($id);
        $pren->delete();

        return redirect(route('officina.prenota'))->withSuccess('Prenotazione eliminata.');
    }

    public function modifica($id)
    {
        $pren = Prenotazioni::find($id);
        $clienti = ViewClienti::orderBy('nominativo', 'asc')->get();
        $usi = Uso::all();
        $meccanici = ViewMeccanici::orderBy('nominativo')->get();

        return view('officina.prenotazioni.modifica', ['pren' => $pren, 'clienti' => $clienti, 'usi' => $usi, 'meccanici' => $meccanici]);
    }

    public function update(Request $request, $id)
    {
        $validRequest = Validator::make($request->all(), [
            'nome' => 'required',
            'veicolo' => 'required',
            'meccanico' => 'required',
            'data_par' => 'required|date',
            'ora_par' => 'required',
            'data_arr' => 'required|date|after_or_equal:data_par',
            'ora_arr' => 'required',
            'uso' => 'required',
            'destinazione' => 'required',
        ]);

        $validRequest->sometimes('ora_arr', 'after:ora_par', function ($input) {
            return $input->data_par == $input->data_arr;
        });

        if ($validRequest->fails()) {
            return redirect(route('officina.prenota.update', $id))->withErrors($validRequest)->withInput();
        }
        $pren = Prenotazioni::find($id);
        $pren->update([
            'cliente_id' => request('nome'),
            'veicolo_id' => request('veicolo'),
            'meccanico_id' => request('meccanico'),
            'data_partenza' => request('data_par'),
            'ora_partenza' => request('ora_par'),
            'data_arrivo' => request('data_arr'),
            'ora_arrivo' => request('ora_arr'),
            'destinazione' => request('destinazione'),
            'uso_id' => request('uso'),
            'note' => request('note'),
        ]);

        return redirect(route('officina.prenota'))->withSuccess('Modifica eseguita.');
    }

    public function all()
    {
        // $prenotazioni = Prenotazioni::where('data_partenza', '>=' , Carbon::now()->toDateString())->orderBy('ora_partenza', 'asc')->get();
        $prenotazioni = Prenotazioni::where('data_arrivo', '<=', Carbon::now()->subWeekday()->toDateString())
            ->orderBy('data_partenza', 'desc')
            ->orderBy('ora_partenza', 'desc')
            ->orderBy('data_arrivo', 'desc')
            ->orderBy('ora_arrivo', 'asc')
            ->get();

        return view('officina.prenotazioni.all', ['prenotazioni' => $prenotazioni]);
    }
}
