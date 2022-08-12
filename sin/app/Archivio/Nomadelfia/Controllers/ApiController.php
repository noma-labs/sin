<?php

namespace App\Nomadelfia\Controllers;

use App\Nomadelfia\Models\PopolazioneNomadelfia;
use Illuminate\Http\Request;
use App\Core\Controllers\BaseController;
use App\Traits\Enums;

//models
use App\Nomadelfia\Models\Famiglia;
use App\Nomadelfia\Models\NucleoFamigliare;
use App\Nomadelfia\Models\Azienda;
use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Models\GruppoFamiliare;

use Carbon;

class ApiController extends BaseController
{
    function persone(Request $request)
    {
        $term = $request->term;
        $persone = Persona::where("nominativo", "LIKE", "$term%")->orderBy("nominativo")->get();
        $results = array();
        foreach ($persone as $persona) {
            $results[] = ['value' => $persona->id, 'label' => $persona->nominativo];
        }
        return response()->json($results);
    }

    function searchPersonaInPopolazioneAttuale(Request $request)
    {
        $persone = PopolazioneNomadelfia::presenteByNomeCognomeNominativo($request->term)
                ->orderBy('nominativo')
                ->get();

        $results = array();
        foreach ($persone as $persona) {
            $year = Carbon\Carbon::createFromFormat('Y-m-d', $persona->data_nascita)->year;
            $results[] = ['value' => $persona->id, 'label' => "($year) $persona->nominativo ($persona->nome  $persona->cognome)"];
        }
        return response()->json($results);
    }




    function persona(Request $request, $id)
    {
        $persona = Persona::findOrFail($id);
        return response()->json($persona);
    }

    function gruppi(Request $request)
    {
        $term = $request->term;
        $gruppi = GruppoFamiliare::where("nome", "LIKE", "$term%")->orderBy("nome")->get();
        $results = array();
        foreach ($gruppi as $gruppo) {
            $results[] = ['value' => $gruppo->id, 'label' => $gruppo->nome];
        }
        return response()->json($results);
    }

    function famiglie(Request $request)
    {
        $term = $request->term;
        $famiglie = Famiglia::where("nome_famiglia", "LIKE", "$term%")->orderBy("nome_famiglia")->get();
        $results = array();
        foreach ($famiglie as $famiglia) {
            $results[] = ['value' => $famiglia->id, 'label' => $famiglia->nome_famiglia];
        }
        return response()->json($results);
    }

    public function famigliaCreate(Request $request)
    {
        return $request->input('nome') . $request->input('cognome');
    }

    public function posizioniAll(Request $request)
    {
        $id_posizioni_nuova_famiglia = array(1, 2, 7, 8);
        $posizioni = NucleoFamigliare::all();
        $results = array();
        foreach ($posizioni as $posizion) {
            $results[] = [
                'id' => $posizion->id,
                'posizione' => $posizion->nucleo_famigliare,
                "stato" => in_array($posizion->id, $id_posizioni_nuova_famiglia)
            ];
        }
        return response()->json($results);
    }

    /**
     * ritorna il json dell'azienda insieme ai lavoratori
     * @param id dell'azienda
     * @author Matteo Neri
     **/
    public function aziendaEdit($id)
    {
        $azienda = Azienda::aziende()->with('lavoratoriAttuali')->with('lavoratoriStorici')->findOrFail($id);
        $results = array(
            [
                'nome' => $azienda->nome_azienda,
                'lavoratori' => $azienda->lavoratoriAttuali,
                "tipo" => $azienda->tipo,
                "lavoratoriStorici" => $azienda->lavoratoriStorici
            ]
        );
        return response()->json($results);
    }

    /**
     * ritorna il json deglin incarichi insieme ai lavoratori
     * @param id incarico
     * @author Matteo Neri
     **/
    public function incarichiEdit($id)
    {
        $azienda = Azienda::incarichi()->with('lavoratoriAttuali')->with('lavoratoriStorici')->findOrFail($id);
        $results = array(
            [
                'nome' => $azienda->nome_azienda,
                'lavoratori' => $azienda->lavoratoriAttuali,
                "tipo" => $azienda->tipo,
                "lavoratoriStorici" => $azienda->lavoratoriStorici
            ]
        );
        return response()->json($results);
    }

    /**
     * ritorna l'array con tutte le possibili mansioni
     **/
    public function mansioni()
    {
        return Enums::getPossibleEnumValues('mansione', 'db_nomadelfia.aziende_persone');
    }


    /**
     * ritorna l'array con tutti i possibili stati
     **/
    public function stati()
    {
        return Enums::getPossibleEnumValues('stato', 'db_nomadelfia.aziende_persone');
    }

    /**
     * Modifica i parametri 'stato', 'data_inizio_azienda', 'data_fine_azienda' e 'mansione' nella tabella pivot
     *
     **/
    public function modificaLavoratore(Request $request)
    {
        $azienda = Azienda::findOrFail($request->input('azienda_id'));

        $attr = [
            'stato' => $request->input('stato'),
            'data_inizio_azienda' => $request->input('data_inizio'),
            'mansione' => $request->input('mansione'),
            'data_fine_azienda' => $request->input('data_fine')
        ];
        if ($azienda->lavoratoriAttuali()->updateExistingPivot($request->input('lavoratore_id'), $attr)) {
            return [true];
        } else {
            return [false];
        }
    }

    /**
     * Ritorna un array con le aziende attuali del lavoratore
     * se filtro=storico le aziende nello storico con lavoratore id
     * se filtro=possibili le aziende dove può lavorare il lavoratore id
     * @param id del lavoratore
     * @return array con i risultati
     * @author Matteo Neri
     **/
    public function aziendeLavoratore(Request $request, $id)
    {
        $results = array();
        if ($request->has('filtro')) {
            // Aziende nello storico
            if ($request->query('filtro') == 'storico') {
                $aziende = Azienda::aziende()->whereHas('lavoratoriStorici', function ($query) use ($id) {
                    $query->where('id', '=', $id);
                })->orderBy('nome_azienda')->get();
                foreach ($aziende as $azienda) {
                    $results[] = ['id' => $azienda->id, 'nome' => $azienda->nome_azienda];
                }
            } // Aziende possibili per il lavoratore
            elseif ($request->query('filtro') == 'possibili') {
                $aziende = Azienda::aziende()->whereDoesntHave('lavoratoriAttuali', function ($query) use ($id) {
                    $query->where('id', '=', $id);
                })->orderBy('nome_azienda')->get();
                foreach ($aziende as $azienda) {
                    $results[] = ['id' => $azienda->id, 'nome' => $azienda->nome_azienda];
                }
            }
        } // Aziende attuali del lavoratore
        else {
            $aziende = Azienda::aziende()->whereHas('lavoratoriAttuali', function ($query) use ($id) {
                $query->where('id', '=', $id);
            })->orderBy('nome_azienda')->get();
            foreach ($aziende as $azienda) {
                $results[] = ['id' => $azienda->id, 'nome' => $azienda->nome_azienda];
            }
        }
        return response()->json($results);
    }

    public function incarichiLavoratore(Request $request, $id)
    {
        $results = array();
        if ($request->has('filtro')) {
            // Aziende nello storico
            if ($request->query('filtro') == 'storico') {
                $aziende = Azienda::incarichi()->whereHas('lavoratoriStorici', function ($query) use ($id) {
                    $query->where('id', '=', $id);
                })->orderBy('nome_azienda')->get();
                foreach ($aziende as $azienda) {
                    $results[] = ['id' => $azienda->id, 'nome' => $azienda->nome_azienda];
                }
            } // Aziende possibili per il lavoratore
            elseif ($request->query('filtro') == 'possibili') {
                $aziende = Azienda::incarichi()->whereDoesntHave('lavoratoriAttuali', function ($query) use ($id) {
                    $query->where('id', '=', $id);
                })->orderBy('nome_azienda')->get();
                foreach ($aziende as $azienda) {
                    $results[] = ['id' => $azienda->id, 'nome' => $azienda->nome_azienda];
                }
            }
        } // Aziende attuali del lavoratore
        else {
            $aziende = Azienda::incarichi()->whereHas('lavoratoriAttuali', function ($query) use ($id) {
                $query->where('id', '=', $id);
            })->orderBy('nome_azienda')->get();
            foreach ($aziende as $azienda) {
                $results[] = ['id' => $azienda->id, 'nome' => $azienda->nome_azienda];
            }
        }
        return response()->json($results);
    }

    /**
     * sposta un lavoratore da un'azienda ad unaltra
     * @return se l'operazione è andata a buon fine
     * @author Matteo Neri
     **/
    public function spostaLavoratore(Request $request)
    {
        $azienda = Azienda::findOrFail($request->input('id_azienda'));
        $nuova_azienda = Azienda::findOrFail($request->input('nuova_azienda_id'));

        $result1 = $azienda->lavoratoriAttuali()->updateExistingPivot($request->input('id_lavoratore'),
            ['stato' => 'Non Attivo', 'data_fine_azienda' => $request->input('data')]);
        $result2 = $nuova_azienda->lavoratori()->attach($request->input('id_lavoratore'),
            ['data_inizio_azienda' => $request->input('data')]);

        return [$result1 && $result2];
    }

    /**
     * sposta un lavoratore da un incarico all'altro
     * @return se l'operazione è a ndata a buon fine
     * @author Matteo Neri
     **/
    public function incarichiSpostaLavoratore(Request $request)
    {
        $azienda = Azienda::incarichi()->findOrFail($request->input('id_azienda'));
        $nuova_azienda = Azienda::incarichi()->findOrFail($request->input('nuova_azienda_id'));

        $result1 = $azienda->lavoratoriAttuali()->updateExistingPivot($request->input('id_lavoratore'),
            ['stato' => 'Non Attivo', 'data_fine_azienda' => $request->input('data')]);
        $result2 = $nuova_azienda->lavoratori()->attach($request->input('id_lavoratore'),
            ['data_inizio_azienda' => $request->input('data')]);

        return [$result1 && $result2];
    }

    public function incarichiAggiungiNuovoLavoratore(Request $request)
    {
        $persona = Persona::findOrFail($request->input('lavoratore_id'));
        $azienda = Azienda::incarichi()->findOrFail($request->input('azienda_id'));
        $persona->assegnaLavoratoreIncarico($azienda, Carbon::parse($request->input('data')));
    }


    /**
     * Aggiunge una persona ad un'azienda
     *
     **/
    public function aggiungiNuovoLavoratore(Request $request)
    {
        $persona = Persona::findOrFail($request->input('lavoratore_id'));
        $azienda = Azienda::findOrFail($request->input('azienda_id'));
        $persona->assegnaLavoratoreAzienda($azienda, $request->input('data'));
    }

    /**
     * ricerca i possibili nomi che hanno il valore inviato all'interno del nominativo
     * @author Matteo Neri
     **/
    public function autocompleteLavoratore(Request $request)
    {
        $azienda_id = $request->input('azienda_id');
        $persone = PopolazioneNomadelfia::presenteByNomeCognomeNominativo($request->input('term'))->get();
        $lavoratori_attivi = Persona::whereHas('aziendeAttuali', function ($query) use ($azienda_id) {
            $query->where('azienda_id', '=', $azienda_id);
        })->get();
        return $persone->filter(function ($value, $key) {
            return $value->id != 0;
        })->diff($lavoratori_attivi)->take(30)->toArray();
    }
}
