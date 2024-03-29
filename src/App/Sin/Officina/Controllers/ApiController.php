<?php

namespace App\Officina\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use App\Officina\Models\Alimentazioni;
use App\Officina\Models\Impiego;
use App\Officina\Models\Marche;
use App\Officina\Models\Prenotazioni;
use App\Officina\Models\TipoFiltro;
use App\Officina\Models\TipoGomme;
use App\Officina\Models\Tipologia;
use App\Officina\Models\Veicolo;
use App\Officina\Models\ViewClienti;
use App\Officina\Models\ViewMeccanici;
use Illuminate\Http\Request;
use Throwable;

class ApiController extends CoreBaseController
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

    public function searchVeicoli(Request $request)
    {
        $term = $request->input('term');
        $veicoli = Veicolo::withTrashed()
            ->where('nome', 'LIKE', '%'.$term.'%')
            ->orWhere('targa', 'LIKE', '%'.$term.'%')
            ->orderBy('nome')
            ->take(50)->get();
        $results = [];
        foreach ($veicoli as $veicolo) {
            $results[] = [
                'value' => $veicolo->id,
                'label' => "$veicolo->nome - $veicolo->targa ".$veicolo->impiego->nome,
            ];
        }

        return response($results);
    }

    // /api/veicoli/prenotazioni?datapartenza=AAAA-MM-YY&ora_in=HH:MM,HH:MM
    // ritorna tutti i veicoli con la liste delle prenotazioni tra ora_in
    public function veicoliPrenotazioni(Request $request)
    {
        // lista delle prenotazioni attive tra l'ora di partenza e l'ora di arrivo
        $IDPrenotazioniAttive = collect();
        if ($request->filled(['datapartenza', 'dataarrivo', 'ora_in'])) {
            $pieces = explode(',', $request->input('ora_in'));
            $orap = $pieces[0];
            $oraa = $pieces[1];
            $datap = $request->input('datapartenza');
            $dataa = $request->input('dataarrivo');

            //prenotazioni attive guardando solo le date: datapartenza e dataarrivo
            $IDPrenotazioniAttiveData = Prenotazioni::with(['cliente'])
                ->where('data_partenza', '<', $dataa)
                ->where('data_arrivo', '>', $datap)
                ->pluck('id');

            // prenotazioni attive guardando le date e le ore
            $IDPrenotazioniAttiveDataOra = Prenotazioni::with(['cliente'])
                ->where('data_arrivo', '=', $datap)
                ->where('data_partenza', '!=', $datap) // elimina partenza nello stesso giorno
                ->where('ora_arrivo', '>', $orap)
                ->pluck('id');

            //prenotazioni attive guardando le date e ore
            $IDPrenotazioniAttiveDataOra2 = Prenotazioni::with(['cliente'])
                ->where('data_partenza', '=', $dataa)
                ->where('data_arrivo', '!=', $dataa) // elimina partenza nello stesso giorno
                ->where('ora_partenza', '<', $oraa)
                ->pluck('id');

            // prenotazioni attive nello stesso giorno guardando l'ora
            $IDPrenotazioniAttiveOggi = Prenotazioni::with(['cliente'])
                ->where('data_partenza', '=', $datap)
                ->where('data_arrivo', '=', $dataa)
                ->where(function ($query) use ($orap, $oraa) {
                    // esclude le prenotazioni che sono a cavallo dell'ora di partenza della nuova prenotazione
                    // $query->where([['ora_partenza', '<=', $orap],['ora_arrivo',">",$orap]])
                    //        // esclude le prenotazioni che sono a cavallo dell'ora di arrivo della nuova prenotazion
                    //       ->orWhere([['ora_partenza', '<', $oraa],['ora_arrivo',">=",$oraa]])
                    //        // esclude le prenotazioni che sono a all'interno dell'ora partenza e arrivo della nuova prenotazion
                    //       ->orWhere([['ora_partenza', '>=', $orap],['ora_arrivo',"<=",$oraa]]);
                    $query->where([['ora_partenza', '<', $oraa], ['ora_arrivo', '>', $orap]]);

                })
                ->pluck('id');

            $IDPrenotazioniAttive = collect();
            $IDPrenotazioniAttive = $IDPrenotazioniAttive->merge($IDPrenotazioniAttiveData);
            $IDPrenotazioniAttive = $IDPrenotazioniAttive->merge($IDPrenotazioniAttiveOggi);
            $IDPrenotazioniAttive = $IDPrenotazioniAttive->merge($IDPrenotazioniAttiveDataOra2);
            $IDPrenotazioniAttive = $IDPrenotazioniAttive->merge($IDPrenotazioniAttiveDataOra);

            // esclude l'id della prenotazione (parametro except) tra le prenotazioni attive.
            // Viene usato quando si sta modificando una prenotazione.
            if ($request->filled('except')) {
                $IDPrenotazioniAttive = $IDPrenotazioniAttive->filter(function ($value, $key) use ($request) {
                    return $value != $request->input('except');
                });
            }

        }
        $veicoliPrenotabili = [];

        // grosseto -> autovettura
        $autovettura_grosseto = Veicolo::with([
            'prenotazioni' => function ($query) use ($IDPrenotazioniAttive) {
                $query->whereIn('id', $IDPrenotazioniAttive);
            },
            'prenotazioni.cliente' => function ($query) {
                $query->select('id', 'nominativo');
            },
        ])->prenotabili()->grosseto()->autovettura()->orderBy('nome');
        $veicoliPrenotabili[] = [
            'impiego_tipologia' => 'Grosseto autovettura',
            'count' => $autovettura_grosseto->count(),
            'veicoli' => $autovettura_grosseto->get(),
        ];

        // grosseto -> furgoni
        $furgoni_grosseto1 = Veicolo::with([
            'prenotazioni' => function ($query) use ($IDPrenotazioniAttive) {
                $query->whereIn('id', $IDPrenotazioniAttive);
            },
            'prenotazioni.cliente' => function ($query) {
                $query->select('id', 'nominativo');
            },
        ])->prenotabili()->grosseto()->furgoni()->orderBy('nome');
        $veicoliPrenotabili[] = [
            'impiego_tipologia' => 'Grosseto furgoni',
            'count' => $furgoni_grosseto1->count(),
            'veicoli' => $furgoni_grosseto1->get(),
        ];

        // grosseto -> Pulmino
        $furgoni_grosseto2 = Veicolo::with([
            'prenotazioni' => function ($query) use ($IDPrenotazioniAttive) {
                $query->whereIn('id', $IDPrenotazioniAttive);
            },
            'prenotazioni.cliente' => function ($query) {
                $query->select('id', 'nominativo');
            },
        ])->prenotabili()->grosseto()->Pulmino()->orderBy('nome');
        $veicoliPrenotabili[] = [
            'impiego_tipologia' => 'Grosseto pulmini',
            'count' => $furgoni_grosseto2->count(),
            'veicoli' => $furgoni_grosseto2->get(),
        ];

        //grosseto motocicli
        $motocicli = Veicolo::with([
            'prenotazioni' => function ($query) use ($IDPrenotazioniAttive) {
                $query->whereIn('id', $IDPrenotazioniAttive);
            },
            'prenotazioni.cliente' => function ($query) {
                $query->select('id', 'nominativo');
            },
        ])->prenotabili()->motocicli()->orderBy('nome');
        $veicoliPrenotabili[] = [
            'impiego_tipologia' => 'Motocicli',
            'count' => $motocicli->count(),
            'veicoli' => $motocicli->get(),
        ];

        // viaggi lunghi autovettura
        $autovettura_lunghi = Veicolo::with([
            'prenotazioni' => function ($query) use ($IDPrenotazioniAttive) {
                $query->whereIn('id', $IDPrenotazioniAttive);
            },
            'prenotazioni.cliente' => function ($query) {
                $query->select('id', 'nominativo');
            },
        ])->prenotabili()->viaggilunghi()->autovettura()->orderBy('nome');
        $veicoliPrenotabili[] = [
            'impiego_tipologia' => 'Viaggi lunghi autovettura',
            'count' => $autovettura_lunghi->count(),
            'veicoli' => $autovettura_lunghi->get(),
        ];

        // viaggi lunghi furgnoi
        $furgoni_lunghi = Veicolo::with([
            'prenotazioni' => function ($query) use ($IDPrenotazioniAttive) {
                $query->whereIn('id', $IDPrenotazioniAttive);
            },
            'prenotazioni.cliente' => function ($query) {
                $query->select('id', 'nominativo');
            },
        ])->prenotabili()->viaggilunghi()->furgoni()->orderBy('nome');
        $veicoliPrenotabili[] = [
            'impiego_tipologia' => 'Viaggi lunghi furgoni',
            'count' => $furgoni_lunghi->count(),
            'veicoli' => $furgoni_lunghi->get(),
        ];

        // viaggi lunghi pulmino
        $furgoni_lunghi1 = Veicolo::with([
            'prenotazioni' => function ($query) use ($IDPrenotazioniAttive) {
                $query->whereIn('id', $IDPrenotazioniAttive);
            },
            'prenotazioni.cliente' => function ($query) {
                $query->select('id', 'nominativo');
            },
        ])->prenotabili()->viaggilunghi()->Pulmino()->orderBy('nome');
        $veicoliPrenotabili[] = [
            'impiego_tipologia' => 'Viaggi lunghi pulmini',
            'count' => $furgoni_lunghi1->count(),
            'veicoli' => $furgoni_lunghi1->get(),
        ];

        //viaggi lunghi autobus
        $autobus = Veicolo::with([
            'prenotazioni' => function ($query) use ($IDPrenotazioniAttive) {
                $query->whereIn('id', $IDPrenotazioniAttive);
            },
            'prenotazioni.cliente' => function ($query) {
                $query->select('id', 'nominativo');
            },
        ])->prenotabili()->viaggilunghi()->autobus()->orderBy('nome');
        $veicoliPrenotabili[] = [
            'impiego_tipologia' => 'Viaggi lunghi Autobus',
            'count' => $autobus->count(),
            'veicoli' => $autobus->get(),
        ];

        // viaggi lunghi autocarri
        $autocarri = Veicolo::with([
            'prenotazioni' => function ($query) use ($IDPrenotazioniAttive) {
                $query->whereIn('id', $IDPrenotazioniAttive);
            },
            'prenotazioni.cliente' => function ($query) {
                $query->select('id', 'nominativo');
            },
        ])->prenotabili()->viaggilunghi()->autocarri()->orderBy('nome');
        $veicoliPrenotabili[] = [
            'impiego_tipologia' => 'Viaggi lunghi Autocarri',
            'count' => $autocarri->count(),
            'veicoli' => $autocarri->get(),
        ];

        // personali
        $personali = Veicolo::with([
            'prenotazioni' => function ($query) use ($IDPrenotazioniAttive) {
                $query->whereIn('id', $IDPrenotazioniAttive);
            },
            'prenotazioni.cliente' => function ($query) {
                $query->select('id', 'nominativo');
            },
        ])->prenotabili()->personali()->orderBy('nome');
        $veicoliPrenotabili[] = [
            'impiego_tipologia' => 'Veicoli Personali',
            'count' => $personali->count(),
            'veicoli' => $personali->get(),
        ];

        // roma
        $roma = Veicolo::with([
            'prenotazioni' => function ($query) use ($IDPrenotazioniAttive) {
                $query->whereIn('id', $IDPrenotazioniAttive);
            },
            'prenotazioni.cliente' => function ($query) {
                $query->select('id', 'nominativo');
            },
        ])->prenotabili()->roma()->orderBy('nome');
        $veicoliPrenotabili[] = [
            'impiego_tipologia' => 'Veicoli Roma',
            'count' => $roma->count(),
            'veicoli' => $roma->get(),
        ];

        return response()->json($veicoliPrenotabili);
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

    public function tipologia(Request $request)
    {
        // $term = $request->input('term');
        $tipologie = Tipologia::all(); //where("nome","LIKE", $term.'%')->get();
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

    public function meccanici(Request $request)
    {
        $term = $request->input('term');
        $meccanici = ViewMeccanici::where('nominativo', 'LIKE', "%$term%")->get();

        $results = [];
        foreach ($meccanici as $meccanico) {
            $results[] = ['value' => $meccanico->persona_id, 'label' => $meccanico->nominativo];
        }

        return response()->json($results);
    }

    /**
     * elimina una tipo di gomma da un veicolo
     */
    public function eliminaGomma(Request $request)
    {
        $veicolo = Veicolo::find($request->input('veicolo'));
        try {
            $veicolo->gomme()->detach($request->input('gomma'));
        } catch (Throwable $th) {
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
    public function nuovaGomma(Request $request)
    {
        if ($request->input('note') == '') {
            $note = '';
        } else {
            $note = $request->input('note');
        }
        if ($request->input('gomma_id') == '') {
            // salvo la nuova gomma nel db
            try {
                $gomma = TipoGomme::create([
                    'codice' => strtoupper($request->input('nuovo_codice')),
                    'note' => $note,
                ]);
            } catch (Throwable $th) {
                return [
                    'status' => 'error',
                    'msg' => "Errore: codice della gomma gia' presente ".$request->input('nuovo_codice').' '.($request->input('note') == ''),
                ];
            }
        } else {
            $gomma = TipoGomme::find($request->input('gomma_id'));
        }
        $veicolo = Veicolo::find($request->input('veicolo_id'));
        try {
            $veicolo->gomme()->attach($gomma->id);
        } catch (Throwable $th) {
            return ['status' => 'error', 'msg' => "Errore: il veicolo ha gia' questo tipo di gomma"];
        }

        return ['status' => 'ok'];
    }

    /**
     * ritorna tutti i filtri nel db
     */
    public function filtri()
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
    public function tipiFiltro()
    {
        $filtri = TipoFiltro::tipo();

        return $filtri;
    }

    /**
     * elimina un filtro dal db
     */
    public function eliminaFiltro(Request $request)
    {
        $filtro = TipoFiltro::find($request->input('filtro'));
        try {
            $filtro->delete();
        } catch (Throwable $th) {
            return ['status' => 'error', 'msg' => "Errore nell'eliminazione del filtro"];
        }

        return ['status' => 'success', 'msg' => 'Filtro eliminato'];
    }
}
