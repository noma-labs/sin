<?php

declare(strict_types=1);

namespace App\Officina\Controllers;

use App\Officina\Models\Alimentazioni;
use App\Officina\Models\Impiego;
use App\Officina\Models\Marche as Marca;
use App\Officina\Models\Modelli as Modello;
use App\Officina\Models\TipoFiltro;
use App\Officina\Models\TipoGomme;
use App\Officina\Models\Tipologia;
use App\Officina\Models\TipoOlio;
use App\Officina\Models\Veicolo;
use Illuminate\Http\Request;
use Throwable;

final class VeicoliController
{
    public function index(Request $request)
    {
        $marche = Marca::orderBy('nome', 'asc')->get();
        $modelli = Modello::orderBy('nome', 'asc')->get();

        $veicoli = Veicolo::orderBy('veicolo.nome', 'asc');
        if ($request->filled('marca')) {
            $veicoli->join('db_meccanica.modello', 'veicolo.modello_id', '=', 'modello.id')
                ->where('modello.marca_id', '=', $request->input('marca'));
        }
        if ($request->filled('nome')) {
            $veicoli->where('veicolo.nome', 'like', $request->input('nome').'%');
        }
        if ($request->filled('targa')) {
            $veicoli->where('veicolo.targa', 'like', '%'.$request->input('targa').'%');
        }
        if ($request->filled('modello')) {
            $veicoli->where('veicolo.modello_id', '=', $request->input('modello'));
        }
        $veicoli = $veicoli->get();

        return view('officina.veicoli.index', compact('veicoli', 'marche', 'modelli'));
    }

    public function show($id)
    {
        $veicolo = Veicolo::withTrashed()->findOrFail($id);
        $gomme = TipoGomme::orderBy('codice')->get();

        return view('officina.veicoli.show', compact('veicolo', 'gomme'));
    }

    public function edit($id)
    {
        $veicolo = Veicolo::withTrashed()->findOrFail($id);
        $marche = Marca::orderBy('nome', 'asc')->get();
        $modelli = Modello::orderBy('nome', 'asc')->get();
        $impieghi = Impiego::orderBy('nome', 'asc')->get();
        $tipologie = Tipologia::orderBy('nome', 'asc')->get();
        $alimentazioni = Alimentazioni::orderBy('nome', 'asc')->get();
        $f_aria = TipoFiltro::where('tipo', '=', 'aria')->orderBy('codice', 'asc')->get();
        $f_olio = TipoFiltro::where('tipo', '=', 'olio')->orderBy('codice', 'asc')->get();
        $f_gasolio = TipoFiltro::where('tipo', '=', 'gasolio')->orderBy('codice', 'asc')->get();
        $f_ac = TipoFiltro::where('tipo', '=', 'ac')->orderBy('codice', 'asc')->get();
        $olio_motore = TipoOlio::all();
        $gomme = TipoGomme::orderBy('codice')->get();

        return view('officina.veicoli.edit', compact('veicolo', 'marche', 'impieghi', 'modelli', 'tipologie', 'alimentazioni', 'f_aria', 'f_olio', 'f_gasolio', 'f_ac', 'olio_motore', 'gomme'));
    }

    public function update(Request $request, $id)
    {
        $input = $request->except(['_token', 'marca_id', '_method']);
        $veicolo = Veicolo::find($id);
        $veicolo->update($input);
        if ($request->filled('marca_id')) {
            $veicolo->modello->marca_id = $request->input('marca_id');
            $veicolo->push();
        }

        return redirect()->route('veicoli.dettaglio', ['id' => $id]);
    }

    public function create()
    {
        $marche = Marca::orderBy('nome', 'asc')->get();
        $impieghi = Impiego::orderBy('nome', 'asc')->get();
        $tipologie = Tipologia::orderBy('nome', 'asc')->get();
        $alimentazioni = Alimentazioni::orderBy('nome', 'asc')->get();
        $f_aria = TipoFiltro::where('tipo', '=', 'aria')->orderBy('codice', 'asc')->get();
        $f_olio = TipoFiltro::where('tipo', '=', 'olio')->orderBy('codice', 'asc')->get();
        $f_gasolio = TipoFiltro::where('tipo', '=', 'gasolio')->orderBy('codice', 'asc')->get();
        $f_ac = TipoFiltro::where('tipo', '=', 'ac')->orderBy('codice', 'asc')->get();

        return view('officina.veicoli.create', compact('marche', 'impieghi', 'tipologie', 'alimentazioni', 'f_aria', 'f_olio', 'f_gasolio', 'f_ac'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required',
            'targa' => 'required',
            'modello' => 'required',
            'marca' => 'required',
            'impiego' => 'required',
            'tipologia' => 'required',
            'alimentazione' => 'required',
            'posti' => 'required',
        ]);

        // Retrieve Modello by name, or create it with the name and marca_id attributes...
        $modello = Modello::firstOrCreate(
            ['nome' => $request->input('modello')], ['marca_id' => $request->input('marca')]
        );

        $veicolo = Veicolo::create([
            'nome' => $request->input('nome'),
            'targa' => $request->input('targa'),
            'modello_id' => $modello->id,
            'impiego_id' => $request->input('impiego'),
            'tipologia_id' => $request->input('tipologia'),
            'alimentazione_id' => $request->input('alimentazione'),
            'num_posti' => $request->input('posti'),
        ]);

        if ($request->input('_addanother')) { // salva e aggiungi un'altro7
            return redirect(route('veicoli.nuovo'))->withSuccess("Veicolo $veicolo->nome salvato correttamente");
        }

        return redirect(route('veicoli.dettaglio', ['id' => $veicolo->id]))->withSuccess("Veicolo $veicolo->nome salvato correttamente");

    }

    public function destroy($id)
    {
        $veicolo = Veicolo::find($id);
        $veicolo->delete();

        return redirect(route('veicoli.index'))->withSuccess("Il veicolo $veicolo->nome è stato demolito");
    }

    public function aggiungiFiltro(Request $request)
    {
        $request->validate([
            'codice' => 'required',
            'tipo' => 'required',
        ]);

        $note = $request->input('note', '');

        // salva il filtro
        try {
            $filtro = TipoFiltro::create([
                'codice' => mb_strtoupper((string) $request->input('codice')),
                'tipo' => $request->input('tipo'),
                'note' => $note,
            ]);
        } catch (Throwable) {
            return redirect(route('veicoli.modifica', ['id' => $request->input('veicolo')]))->withError('Errore durante il salvataggio del filtro: filtro già esistente');
        }

        if ($filtro) {
            return redirect(route('veicoli.modifica', ['id' => $request->input('veicolo')]))->withSuccess("Filtro $filtro->codice salvato correttamente");
        }

        return redirect(route('veicoli.modifica', ['id' => $request->input('veicolo')]))->withError("Errore durante il salvataggio del filtro $filtro->codice");

    }

    public function aggiungiOlio(Request $request)
    {
        $request->validate([
            'codice' => 'required',
        ]);

        $note = $request->input('note', '');

        try {
            $olio = TipoOlio::create([
                'codice' => mb_strtoupper((string) $request->input('codice')),
                'note' => $note,
            ]);
        } catch (Throwable) {
            return redirect(route('veicoli.modifica', ['id' => $request->input('veicolo')]))->withError("Errore durante il salvataggio dell'olio: olio già esistente");
        }

        if ($olio) {
            return redirect(route('veicoli.modifica', ['id' => $request->input('veicolo')]))->withSuccess("Olio $olio->codice salvato correttamente");
        }

        return redirect(route('veicoli.modifica', ['id' => $request->input('veicolo')]))->withError("Errore durante il salvataggio dell'olio $olio->codice");

    }

    public function aggiungiGomma(Request $request)
    {
        $request->validate([
            'codice' => 'required',
        ]);
        $gomma = TipoGomme::create([
            'codice' => $request->input('codice'),
        ]);

        return redirect()->back()->withSuccess("Gomma $gomma->codice salvata correttamente");
    }
}
