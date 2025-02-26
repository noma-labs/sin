<?php

declare(strict_types=1);

namespace App\Agraria\Controllers;

use App\Agraria\Models\Gomma;
use App\Agraria\Models\MezzoAgricolo;
use App\Agraria\Models\StoricoOre;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

final class MezziController
{
    public function show($id)
    {
        $mezzo = MezzoAgricolo::find($id);
        $prossime = $mezzo->scadenzaManutenzioni()->sort();

        return view('agraria.mezzi.show', compact('mezzo', 'prossime'));
    }

    public function edit($id)
    {
        $mezzo = MezzoAgricolo::find($id);

        return view('agraria.mezzi.edit', compact('mezzo'));
    }

    public function editConfirm(Request $request)
    {
        $mezzo_old = MezzoAgricolo::find($request->input('id'));

        $rules = [
            'nome' => 'required',
            'telaio' => ['required',
                Rule::unique('mezzo_agricolo', 'numero_telaio')->ignore($request->input('id'))],
            'ore' => 'required|gte:'.$mezzo_old->tot_ore,
        ];
        if ($request->input('gomme_ant')) {
            $rules['gomme_ant'] = 'regex:/(\d+)\/(\d+)\s(\S+)/';
        }
        if ($request->input('gomme_post')) {
            $rules['gomme_post'] = 'regex:/(\d+)\/(\d+)\s(\S+)/';
        }

        $msg = [
            'nome.required' => 'Il nome del mezzo è richiesto',
            'ore.gte' => 'Le ore lavorative del mezzo non possono essere minori di quelle inserite in precedenza',
            'gomme_ant.regex' => 'La sigla delle gomme deve essere della forma: "larghezza/altezza diametro"',
            'gomme_post.regex' => 'La sigla delle gomme deve essere della forma: "larghezza/altezza diametro"',
            'telaio.required' => 'Il numero di telaio è richiesto',
            'telaio.unique' => 'Il numero di telaio inserito è già presente su un altro mezzo',
        ];

        Validator::make($request->all(), $rules, $msg)->validate();

        $mezzo_old->nome = mb_strtoupper($request->input('nome'));
        $mezzo_old->numero_telaio = mb_strtoupper($request->input('telaio'));
        $mezzo_old->tot_ore = mb_strtoupper($request->input('ore'));
        if ($request->filled('olio')) {
            $mezzo_old->filtro_olio = mb_strtoupper($request->input('olio'));
        }
        if ($request->filled('gasolio')) {
            $mezzo_old->filtro_gasolio = mb_strtoupper($request->input('gasolio'));
        }
        if ($request->filled('servizi')) {
            $mezzo_old->filtro_servizi = mb_strtoupper($request->input('servizi'));
        }
        if ($request->filled('aria_int')) {
            $mezzo_old->filtro_aria_int = mb_strtoupper($request->input('aria_int'));
        }
        if ($request->filled('aria_ext')) {
            $mezzo_old->filtro_aria_ext = mb_strtoupper($request->input('aria_ext'));
        }
        if ($request->filled('gomme_ant')) {
            $gomme_ant = Gomma::where('nome', mb_strtoupper($request->input('gomme_ant')))->first();
            if (is_null($gomme_ant)) {
                $gomme_ant = Gomma::create(['nome' => mb_strtoupper($request->input('gomme_ant'))]);
            }
            $mezzo_old->gomme_ant = $gomme_ant->id;
        } else {
            $mezzo_old->gomme_ant = null;
        }

        if ($request->filled('gomme_post')) {
            $gomme_post = Gomma::where('nome', mb_strtoupper($request->input('gomme_post')))->first();
            if (is_null($gomme_post)) {
                $gomme_post = Gomma::create(['nome' => mb_strtoupper($request->input('gomme_post'))]);
            }
            $mezzo_old->gomme_post = $gomme_post->id;
        } else {
            $mezzo_old->gomme_post = null;
        }
        $mezzo_old->save();

        return redirect()->route('mezzo.show', ['id' => $mezzo_old->id])->withSuccess('Mezzo modificato con successo');
    }

    public function insertConfirm(Request $request)
    {
        $rules = [
            'nome' => 'required',
            'telaio' => 'required|unique:mezzo_agricolo,numero_telaio',
            'ore' => 'required|gte:0',
        ];
        if ($request->filled('gomme_ant')) {
            $rules['gomme_ant'] = 'regex:/(\d+)\/(\d+)\s(\S+)/';
        }
        if ($request->filled('gomme_post')) {
            $rules['gomme_post'] = 'regex:/(\d+)\/(\d+)\s(\S+)/';
        }

        $msg = [
            'nome.required' => 'Il nome del mezzo è richiesto',
            'ore.gte' => 'Le ore lavorative del mezzo non possono essere minori di 0',
            'gomme_ant.regex' => 'La sigla delle gomme deve essere della forma: "larghezza/altezza diametro"',
            'gomme_post.regex' => 'La sigla delle gomme deve essere della forma: "larghezza/altezza diametro"',
            'telaio.required' => 'Il numero di telaio è richiesto',
            'telaio.unique' => 'Il numero di telaio inserito è già presente su un altro mezzo',
        ];

        Validator::make($request->all(), $rules, $msg)->validate();

        if ($request->filled('gomme_ant')) {
            $gomme_ant = Gomma::where('nome', mb_strtoupper($request->input('gomme_ant')))->first();
            if (is_null($gomme_ant)) {
                $gomme_ant = Gomma::create(['nome' => mb_strtoupper($request->input('gomme_ant'))]);
            }
        }

        if ($request->filled('gomme_post')) {
            $gomme_post = Gomma::where('nome', mb_strtoupper($request->input('gomme_post')))->first();
            if (is_null($gomme_post)) {
                $gomme_post = Gomma::create(['nome' => mb_strtoupper($request->input('gomme_post'))]);
            }
        }

        $mezzo = new MezzoAgricolo;
        $mezzo->nome = mb_strtoupper($request->input('nome'));
        $mezzo->numero_telaio = mb_strtoupper($request->input('telaio'));
        $mezzo->tot_ore = mb_strtoupper($request->input('ore'));
        if ($request->filled('olio')) {
            $mezzo->filtro_olio = mb_strtoupper($request->input('olio'));
        }
        if ($request->filled('gasolio')) {
            $mezzo->filtro_gasolio = mb_strtoupper($request->input('gasolio'));
        }
        if ($request->filled('servizi')) {
            $mezzo->filtro_servizi = mb_strtoupper($request->input('servizi'));
        }
        if ($request->filled('aria_int')) {
            $mezzo->filtro_aria_int = mb_strtoupper($request->input('aria_int'));
        }
        if ($request->filled('aria_ext')) {
            $mezzo->filtro_aria_ext = mb_strtoupper($request->input('aria_ext'));
        }
        if (isset($gomme_ant)) {
            $mezzo->gomme_ant = $gomme_ant->id;
        }
        if (isset($gomme_post)) {
            $mezzo->gomme_post = $gomme_post->id;
        }
        $mezzo->save();

        return redirect()->route('mezzo.show', ['id' => $mezzo->id])->withSuccess('Mezzo aggiunto con successo');
    }

    public function aggiornaOreShow()
    {
        $mezzi = MezzoAgricolo::orderBy('nome', 'asc')->get();

        return view('agraria.mezzi.ore', compact('mezzi'));
    }

    public function aggiornaOre(Request $request)
    {
        $mezzi = MezzoAgricolo::all();
        $rules = [];
        $msg = [];
        foreach ($mezzi as $m) {
            $rules['id'.$m->id] = 'required|gte:'.$m->tot_ore;
            $msg['id'.$m->id.'.required'] = 'Le ore per il mezzo '.$m->nome.' sono richieste';
            $msg['id'.$m->id.'.gte'] = 'Le ore per il mezzo '.$m->nome.' devono essere maggiori di o uguali '.$m->tot_ore;
        }

        $val = Validator::make($request->all(), $rules, $msg);
        if ($val->fails()) {
            return redirect(route('mezzi.aggiorna.ore'))->withErrors($val)->withInput();
        }

        foreach ($mezzi as $m) {
            $so = new StoricoOre;
            $so->data = Carbon::today()->toDateString();
            $so->ore = $request->input('id'.$m->id) - $m->tot_ore;
            $so->mezzo_agricolo = $m->id;

            $m->tot_ore = $request->input('id'.$m->id);
            try {
                $m->save();
                $so->save();
            } catch (Throwable) {
                $errors = collect(['Errore salvataggio ore per il mezzo '.$m->nome]);

                return redirect()->route('mezzi.aggiorna.ore')->withErrors($errors)->withInput();
            }

        }

        return redirect()->route('agraria.index')->withSuccess('Ore aggiornate correttamente');
    }
}
