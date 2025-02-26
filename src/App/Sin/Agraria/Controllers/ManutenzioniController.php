<?php

declare(strict_types=1);

namespace App\Agraria\Controllers;

use App\Agraria\Models\Manutenzione;
use App\Agraria\Models\ManutenzioneProgrammata;
use App\Agraria\Models\MezzoAgricolo;
use App\Agraria\Models\StoricoOre;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

final class ManutenzioniController
{
    public function programmateShow()
    {
        return view('agraria.manutenzioni.programmate');
    }

    public function programmateSave(Request $request)
    {
        $rules = [
            'data.nome' => 'required',
            'data.ore' => 'required|gte:0',
        ];

        $msg = [
            'data.nome.required' => 'Il nome della manutenzione è richiesto',
            'data.ore.required' => 'Le ore della manutenzione sono richieste',
            'data.ore.gte' => 'Le ore della manutenzione devono essere maggiori o uguali a 0',
        ];

        $validator = Validator::make($request->all(), $rules, $msg);
        if ($validator->fails()) {
            return ['result' => 'error', 'msg' => $validator->errors()];
        }
        if ($request->filled('data.id')) {
            $man = ManutenzioneProgrammata::find($request->input('data.id'));

            $man->nome = mb_strtoupper($request->input('data.nome'));
            $man->ore = $request->input('data.ore');
            $man->save();
        }

        return ['result' => 'success'];
    }

    public function nuovaShow()
    {
        $mezzi = MezzoAgricolo::orderBy('nome', 'asc')->get();
        $programmate = ManutenzioneProgrammata::orderBy('nome', 'asc')->get();

        return view('agraria.manutenzioni.nuova', compact('mezzi', 'programmate'));
    }

    public function nuovaSave(Request $request)
    {
        // validazione input
        $rules = [
            'mezzo' => 'required',
            'data' => 'required',
            'ore' => 'required',
            'persona' => 'required',
        ];
        if ($request->filled('mezzo')) {
            $mezzo = MezzoAgricolo::find($request->input('mezzo'));
            // $rules['ore'] = 'required|gte:'.$mezzo->tot_ore;
        }
        if (! $request->filled('programmate') && ! $request->filled('straordinarie')) {
            return ['state' => 'error', 'msg' => ['Almeno una manutenzione programmata o straordinaria deve essere fornita']];
        }

        $msg = [
            'mezzo.required' => 'Il mezzo è richiesto',
            'data.required' => 'La data è richiesta',
            'ore.required' => 'Le ore sono richieste',
            // 'ore.gte' => 'Le ore devono essere più di '.$mezzo->tot_ore,
            'persona.required' => 'La persona è richiesta',
        ];

        $validator = Validator::make($request->all(), $rules, $msg);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $error_msg = [];
            foreach ($errors->all() as $message) {
                $error_msg[] = $message;
            }

            return ['state' => 'error', 'msg' => $error_msg];
        }

        // upload della ricevuta, se presente
        $media = null;
        if ($request->hasFile('file')) {
            throw new Exception('Not implemented');
            // try {
            //     $media = MediaUploader::fromSource($request->file('file'))
            //                         ->setAllowedExtensions(['pdf'])
            //                         ->verifyFile();
            // } catch (\Throwable $th) {
            //     return ['state' => 'error','msg' => ['La ricevuta deve essere un file .pdf']];
            // }
            // $media = MediaUploader::fromSource($request->file('file'))
            //                 ->toDirectory('ricevute/')
            //                 ->useHashForFilename()
            //                 ->upload();
        }

        if ($mezzo->tot_ore < $request->input('ore')) {
            // aggiorno le ore nello storico, se sono cambiate
            $so = new StoricoOre;
            $so->data = Carbon::today()->toDateString();
            $so->ore = $request->input('ore') - $mezzo->tot_ore;
            $so->mezzo_agricolo = $mezzo->id;
            $so->save();
            // aggiorno le ore del trattore
            $mezzo->tot_ore = $request->input('ore');
            $mezzo->save();
        }
        // creazione e salvataggio della manutenzione
        $nuova_manutenzione = new Manutenzione;
        $nuova_manutenzione->data = $request->input('data');
        $nuova_manutenzione->ore = $request->input('ore');
        if ($request->filled('spesa')) {
            $nuova_manutenzione->spesa = $request->input('spesa');
        } else {
            $nuova_manutenzione->spesa = 0;
        }
        $nuova_manutenzione->persona = $request->input('persona');
        if ($request->filled('straordinarie')) {
            $nuova_manutenzione->lavori_extra = $request->input('straordinarie');
        } else {
            $nuova_manutenzione->lavori_extra = null;
        }
        $nuova_manutenzione->mezzo_agricolo = $request->input('mezzo');

        try {
            $nuova_manutenzione->save();
        } catch (Throwable $th) {
            return ['state' => 'error', 'msg' => ['Errore salvataggio manutenzione']];
        }

        // attach delle manutenzioni programmate alla manutenzione
        if ($request->filled('programmate')) {
            foreach (explode(',', $request->input('programmate')) as $p) {
                $nuova_manutenzione->programmate()->attach($p);
            }
        }

        // attach della ricevuta alla manutenzione
        if ($media !== null) {
            $nuova_manutenzione->attachMedia($media, 'ricevuta');
        }

        return ['state' => 'success', 'redirect' => route('home')];
    }

    public function ricerca(Request $request)
    {
        if ($request->filled('id')) {
            $mezzo = MezzoAgricolo::find($request->input('id'));

            return view('agraria.manutenzioni.ricerca', compact('mezzo'));
        }

        return view('agraria.manutenzioni.ricerca');
    }
}
