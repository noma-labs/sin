<?php

declare(strict_types=1);

namespace App\Officina\Controllers;

use App\Nomadelfia\Persona\Models\Persona;
use App\Officina\Actions\CreatePrenotazioneAction;
use App\Officina\Models\Prenotazioni;
use App\Officina\Models\Uso;
use App\Officina\Models\Veicolo;
use App\Officina\Models\ViewClienti;
use App\Officina\Models\ViewMeccanici;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

final class PrenotazioniController
{
    public function index(Request $request)
    {
        $day = $request->get('day', 'oggi');

        $clienti = ViewClienti::orderBy('nominativo', 'asc')->get();
        $usi = Uso::all();
        $meccanici = ViewMeccanici::orderBy('nominativo')->get();

        $query = null;
        $now = Carbon::now();
        // TODO: usare le PrenotazioneQueryBulders per prendere prenotazioni attive
        if ($day === 'oggi') {
            // $query = Prenotazioni::today();
            $query = Prenotazioni::where('data_partenza', '=', $now->toDateString())
                ->orWhere('data_arrivo', '=', $now->toDateString());
        } else {
            if ($day === 'ieri') {
                // $query = Prenotazioni::yesterday();
                $query = Prenotazioni::where('data_arrivo', '=', $now->subDay()->toDateString());
            }
            if ($day === 'all') {
                // include:
                //   1) prenotazioni che partono dopo oggi (o uguale)
                //   2) prenotazioni a cavallo di oggi
                //   3) prenotazioni che si concludono oggi
                $query = Prenotazioni::where('data_partenza', '>=', $now->toDateString())
                    ->orWhere(function ($query) use ($now): void {
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

        return view('officina.prenotazioni.index', compact('clienti',
            'usi',
            'meccanici',
            'prenotazioni',
            'day'));
    }

    public function store(Request $request)
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
            'note' => 'nullable|string',
        ]);

        $validRequest->sometimes('ora_arr', 'after:ora_par', fn ($input): bool => $input->data_par === $input->data_arr);

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
            $request->input('note', ''),
            $request->input('destinazione', '')
        );

        return redirect()->back()->withSuccess('Prenotazione eseguita.');

    }

    public function delete($id)
    {
        $pren = Prenotazioni::find($id);
        $pren->delete();

        return redirect(route('officina.prenota'))->withSuccess('Prenotazione eliminata.');
    }

    public function edit($id)
    {
        $pren = Prenotazioni::find($id);
        $clienti = ViewClienti::orderBy('nominativo', 'asc')->get();
        $usi = Uso::all();
        $meccanici = ViewMeccanici::orderBy('nominativo')->get();

        return view('officina.prenotazioni.modifica', compact('pren', 'clienti', 'usi', 'meccanici'));
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

        $validRequest->sometimes('ora_arr', 'after:ora_par', fn ($input): bool => $input->data_par === $input->data_arr);

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

        return redirect()->back()->withSuccess('Modifica eseguita.');
    }
}
