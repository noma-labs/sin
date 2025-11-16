<?php

declare(strict_types=1);

namespace App\Officina\Controllers;

use App\Nomadelfia\Persona\Models\Persona;
use App\Officina\Models\Prenotazioni;
use App\Officina\Models\Uso;
use App\Officina\Models\Veicolo;
use App\Officina\Models\ViewClienti;
use App\Officina\Models\ViewMeccanici;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Validator;

final class ReservationsController
{
    public function create(Request $request): View
    {
        $day = $request->get('day', 'oggi');

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

        return view('officina.reservations.create', compact(
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

        Prenotazioni::create([
            'cliente_id' => Persona::findOrFail($request->get('nome'))->id,
            'veicolo_id' => Veicolo::findOrFail($request->get('veicolo'))->id,
            'meccanico_id' => Persona::findOrFail($request->get('meccanico'))->id,
            'data_partenza' => $request->get('data_par'),
            'ora_partenza' => $request->get('ora_par'),
            'data_arrivo' => $request->get('data_arr'),
            'ora_arrivo' => $request->get('ora_arr'),
            'uso_id' => Uso::findOrFail($request->get('uso'))->ofus_iden,
            'note' => $request->input('note', ''),
            'destinazione' => $request->input('destinazione', ''),
        ]);

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

        return view('officina.reservations.edit', compact('pren', 'clienti', 'usi', 'meccanici'));
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
