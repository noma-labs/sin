<?php

namespace App\Biblioteca\Controllers;

//use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request;
use Carbon;

use App\Core\Controllers\BaseController as CoreBaseController;

use App\Biblioteca\Models\Libro as Libro;
use App\Biblioteca\Models\ViewClientiBiblioteca;
use App\Biblioteca\Models\Autore as Autore;
use App\Biblioteca\Models\Editore as Editore;
use App\Biblioteca\Models\ViewCollocazione as ViewCollocazione;


class ApiController extends CoreBaseController
{

    public function autocompleteLibro(Request $request)
    {
        $term = $request->term;
        $libri = Libro::where("titolo", "like", "%$term%")->orWhere('collocaione', "like", "$term%");
        if ($libri->count() > 0) {
            foreach ($libri as $libro) {
                $results[] = ['value' => $libro->id, 'label' => $libro->collocazione];
            }
            return response()->json($results);
        } else {
            return response()->json(['value' => "", 'label' => "libro non trovato"]);
        }

    }

    public function autocompleteCliente(Request $request)
    {
        $term = $request->term;
        if ($term) {
            $persone = ViewClientiBiblioteca::where("nominativo", "LIKE", "$term%")->orderBy("nominativo")->get();
        }
        if ($persone->count() > 0) {
            foreach ($persone as $persona) {
                $year = Carbon\Carbon::createFromFormat('Y-m-d', $persona->data_nascita)->year;
                $results[] = ['value' => $persona->id, 'label' => "$persona->nominativo ($year)"];
            }
            return response()->json($results);
        } else {
            return response()->json(['value' => "", 'label' => "persona non esiste"]);
        }

    }

    public function autocompleteCollocazione(Request $request)
    {
        // ?term=q       => return all the lettere that start with "q"
        // ?lettere=XXX  => return the numeri (liberi, asseganti, nuovo numero) for the selected lettere
        // ?lettere=XXX&soloassegnati=true => return only the numbers assigned for the letters
        if ($request->has('term')) {
            $CollocazioneLettere = ViewCollocazione::lettere()
                ->where("lettere", "LIKE", $request->input('term') . '%')
                ->get();
            $results[] = ['value' => "null", 'label' => "SENZA COLLOCAZIONE"];
            foreach ($CollocazioneLettere as $lettere) {
                $results[] = ['value' => $lettere->lettere, 'label' => $lettere->lettere];
            }//'id' => $query->id, 'value' => $query->first_name.' '.$query->last_name ];
            return response()->json($results);
        }
        if ($request->has('lettere')) {
            $lettere = $request->input('lettere');
            $max = ViewCollocazione::MaxForLettere($lettere); // max numero associated with the lettere
            $numeri = ViewCollocazione::numeri($lettere)->get()->pluck("numeri")->toArray();
            $arr2 = range(1, $max);
            $res = array_diff($arr2, $numeri);

            $result = [
                "numeriAssegnati" => ($request->input('assegnati', "true") === "true") ? $numeri : null,
                //'Off';  // $numeri,
                "numeriMancanti" => ($request->input('mancanti', "true") === "true") ? $res : null,
                "numeroNuovo" => ($request->input('nuovo', "true") === "true") ? $max + 1 : null
            ];
            return response()->json($result);
        }
    }

    public function autocompleteAutori(Request $request)
    {
        $term = $request->input('term');
        $autori = Autore::where("autore", "LIKE", '%' . $term . '%')->orderBy("autore")->take(50)->get();
        $results = array();
        foreach ($autori as $autore) {
            $results[] = ['value' => $autore->id, 'label' => $autore->autore];
        }
        return response()->json($results);
    }

    public function autocompleteEditori(Request $request)
    {
        $term = $request->input('term');
        $editori = Editore::where("Editore", "LIKE", '%' . $term . '%')->orderBy("editore")->take(50)->get();
        $results = array();
        foreach ($editori as $editore) {
            $results[] = ['value' => $editore->id, 'label' => $editore->editore];
        }
        return response()->json($results);
    }

    public function autocompleteTitolo(Request $request)
    {
        $term = $request->input('term');
        $libri = Libro::withTrashed()->select("titolo")->where("titolo", "LIKE",
            $term . '%')->groupBy('titolo')->take(50)->get();
        $results = array();
        foreach ($libri as $libro) {
            $results[] = ['value' => $libro->titolo, 'label' => $libro->titolo];
        }
        return response()->json($results);
    }

    /**
     * Inserisce un nuovo autore.
     *
     * @param String nome: nome e cognome dell'autore.
     * @return json
     *            {
     *            "err": 0|1, // 1 if there are errors, 0 otherwise
     *            "data": {
     *               "label": String,  // nome of the autore inserted
     *                "value": Int,    // ID of the autore
     *             },
     *             "msg": String  // message  "Editore DIDO-EDITORE-2 esiste già."
     *            }
     */
    public function postAutore(Request $request)
    {
        if ($request->filled('nome')) {
            $nome = $request->input('nome');
            $autore = Autore::where('autore', $nome)->first();
            if (!$autore) {
                $autore = Autore::create(['autore' => $nome]);
                $msg = "Autore $autore->autore inserito correttamente";
                return response()->json([
                    'err' => 0,
                    'data' => ["label" => $autore->autore, "value" => $autore->id],
                    'msg' => $msg
                ]);
            } else {
                $msg = "Autore $autore->autore esiste già.";
            }
            return response()->json(['err' => 1, 'data' => [], 'msg' => $msg]);
        } else {
            return response()->json([
                'err' => 1,
                'error' => "Errore nell'inserimento dellì'autore."
            ], 400);
        };
    }

    /**
     * Inserisce un nuovo editore.
     *
     * @param String nome: nome dell'editore.
     * @return json
     * {
     * "err": 0|1,            // 1 if there are errors, 0 otherwise
     * "data": {
     *     "label": String, //nome of the editore inserted
     *     "value": Int,    // ID of the editore
     *  },
     *  "msg": string
     * }
     */

    public function postEditore(Request $request)
    {
        if ($request->filled('nome')) {
            $nome = $request->input('nome');
            $editore = Editore::where('editore', $nome)->first();
            if (!$editore) {
                $editore = Editore::create(['editore' => $nome]);
                $msg = "Editore $editore->editore inserito correttamente";
                return response()->json([
                    'err' => 0,
                    'data' => ["label" => $editore->editore, "value" => $editore->id],
                    'msg' => $msg
                ]);
            } else {
                $msg = "Editore $editore->editore esiste già.";
            }
            return response()->json(['err' => 1, 'data' => [], 'msg' => $msg]);
        } else {
            return response()->json([
                'err' => 1,
                'msg' => "Errore nell'inserimento dell'editore."
            ], 400);
        };
    }


}
