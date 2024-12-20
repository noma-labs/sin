<?php

declare(strict_types=1);

namespace App\Officina\Controllers;

use App\Officina\Models\Alimentazioni;
use App\Officina\Models\Impiego;
use App\Officina\Models\Marche;
use App\Officina\Models\TipoFiltro;
use App\Officina\Models\TipoGomme;
use App\Officina\Models\Tipologia;
use App\Officina\Models\Veicolo;
use App\Officina\Models\ViewClienti;
use Illuminate\Http\Request;
use Throwable;

final class ApiController
{
    public function clientiMeccanica(Request $request)
    {
        $term = $request->input('term');
        $clienti = ViewClienti::where('nominativo', 'LIKE', $term.'%')->orderBy('nominativo')->take(50)->get();
        $results = [];
        foreach ($clienti as $persona) {
            $results[] = ['value' => $persona->id, 'label' => $persona->nominativo];
        }

        return response()->json($results);
    }

    public function marche(Request $request)
    {
        $term = $request->input('term');
        $marcheWithModelli = Marche::with('modelli')->where('nome', 'LIKE', '%'.$term.'%')->get();
        $results = [];
        foreach ($marcheWithModelli as $marca) {
            $results[] = ['value' => $marca->id, 'label' => $marca->nome, 'modelli' => $marca->modelli->all()];
        }

        return response()->json($results);
    }

    public function impiego(Request $request)
    {
        $term = $request->input('term');
        $impieghi = Impiego::where('nome', 'LIKE', $term.'%')->get();
        $results = [];
        foreach ($impieghi as $impiego) {
            $results[] = ['value' => $impiego->id, 'label' => $impiego->nome];
        }

        return response()->json($results);
    }

    public function tipologia()
    {
        // $term = $request->input('term');
        $tipologie = Tipologia::all();
        //where("nome","LIKE", $term.'%')->get();
        $results = [];
        foreach ($tipologie as $tipologia) {
            $results[] = ['value' => $tipologia->id, 'label' => $tipologia->nome];
        }

        return response()->json($results);
    }

    public function alimentazione(Request $request)
    {
        $term = $request->input('term');
        $alimentazioni = Alimentazioni::where('nome', 'LIKE', $term.'%')->get();
        $results = [];
        foreach ($alimentazioni as $alimentazione) {
            $results[] = ['value' => $alimentazione->id, 'label' => $alimentazione->nome];
        }

        return response()->json($results);
    }

    /**
     * elimina una tipo di gomma da un veicolo
     */
    public function eliminaGomma(Request $request): array
    {
        $veicolo = Veicolo::find($request->input('veicolo'));
        try {
            $veicolo->gomme()->detach($request->input('gomma'));
        } catch (Throwable) {
            return ['error'];
        }

        return ['success'];
    }

    /**
     * ritorna tutte i tipi di gomme nel db
     */
    public function gomme()
    {
        $gomme = TipoGomme::orderBy('codice')->get();
        $result = [];
        foreach ($gomme as $gomma) {
            $result[] = ['codice' => $gomma->codice.' '.$gomma->note, 'id' => $gomma->id];
        }

        return response()->json($result);
    }

    /**
     * salva una nuova gomma e la lega ad un veicolo
     */
    public function nuovaGomma(Request $request): array
    {
        if ($request->input('note') === '') {
            $note = '';
        } else {
            $note = $request->input('note');
        }
        if ($request->input('gomma_id') === '') {
            // salvo la nuova gomma nel db
            try {
                $gomma = TipoGomme::create([
                    'codice' => mb_strtoupper($request->input('nuovo_codice')),
                    'note' => $note,
                ]);
            } catch (Throwable) {
                return [
                    'status' => 'error',
                    'msg' => "Errore: codice della gomma gia' presente ".$request->input('nuovo_codice').' '.($request->input('note') === ''),
                ];
            }
        } else {
            $gomma = TipoGomme::find($request->input('gomma_id'));
        }
        $veicolo = Veicolo::find($request->input('veicolo_id'));
        try {
            $veicolo->gomme()->attach($gomma->id);
        } catch (Throwable) {
            return ['status' => 'error', 'msg' => "Errore: il veicolo ha gia' questo tipo di gomma"];
        }

        return ['status' => 'ok'];
    }

    /**
     * ritorna tutti i filtri nel db
     *
     * @return mixed[]
     */
    public function filtri(): array
    {
        $filtri = TipoFiltro::all()->sortBy('tipo');
        $result = [];
        foreach ($filtri as $value) {
            $result[] = $value;
        }

        return $result;
    }

    /**
     * ritorna tutti i tipi di filtro
     */
    public function tipiFiltro(): array
    {
        return TipoFiltro::tipo();
    }

    /**
     * elimina un filtro dal db
     */
    public function eliminaFiltro(Request $request): array
    {
        $filtro = TipoFiltro::find($request->input('filtro'));
        try {
            $filtro->delete();
        } catch (Throwable) {
            return ['status' => 'error', 'msg' => "Errore nell'eliminazione del filtro"];
        }

        return ['status' => 'success', 'msg' => 'Filtro eliminato'];
    }
}
