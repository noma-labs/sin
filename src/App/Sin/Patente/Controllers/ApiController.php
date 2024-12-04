<?php

declare(strict_types=1);

namespace App\Patente\Controllers;

use App\Patente\Models\CategoriaPatente;
use App\Patente\Models\CQC;
use App\Patente\Models\Patente;
use App\Patente\Models\ViewClientiConSenzaPatente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ApiController
{
    /**
     * Dato il numero di una patente, ritorna la patente e le categorie associate
     *
     * @param  string  $numero  della patente
     * @return string $Patente
     *                {
     *                "persona_id": number,
     *                "numero_patente": string,
     *                "rilasciata_dal": string,
     *                "data_rilascio_patente": date  "GG-MM-YYYY",
     *                "data_scadenza_patente":date "GG-MM-YYYY",
     *                "stato": enum ('commissione',NULL),
     *                "note": string,
     *                "categorie":[
     *                {"id": number,
     *                "categoria": string,
     *                "descrizione": string,
     *                "note":"string,
     *                "pivot":{
     *                "numero_patente": string,
     *                "categoria_patente_id":string,
     *                "data_rilascio":date ("2016-01-07")
     *                "data_scadenza": date ("2021-01-05)
     *                }
     *                }
     *                ]}
     *
     * @author Davide Neri
     */
    public function patente(Request $request, $numero)
    {
        $p = Patente::where('numero_patente', $numero)->with(['categorie', 'cqc'])->first();

        return response()->json($p);
    }

    /**
     * Ritorna tutte le categorie (eccetto i CQC)
     *
     * @return string $CategoriaPatente
     *
     * @author Davide Neri
     */
    public function categorie()
    {
        $categorie = CategoriaPatente::orderby('categoria')->get();

        return response()->json($categorie);
    }

    /**
     * Ritorna cqc merci e c.q.c persone
     *
     *
     * @return string
     *                {[
     *                id: 16,
     *                categoria: "C.Q.C. PERSONE",
     *                descrizione: "PER TRASPORTO PERSONE (IN VIGORE DAL 10/09/2008)",
     *                note: ""]
     *                }
     *
     * @author Davide Neri
     */
    public function cqc()
    {
        $cqc = CQC::orderby('categoria')->get();

        return response()->json($cqc);
    }

    /**
     * Ritorna tutte le restrizioni
     *
     *
     * @return string $Restrizione
     *
     * @author Davide Neri
     */
    public function restrizioni()
    {
        $categorie = CategoriaPatente::orderby('categoria')->get();

        return response()->json($categorie);
    }

    /**
     * Ritorna solo le categorie associate a una patente.
     *    /?filtro=possibili : ritorna le categorie non ancora assegnate alla patente
     *
     * @param  string  $numero  numeor della patente
     * @return JsonResponse
     *
     * @author Davide Neri
     **/
    public function patenteCategorie(Request $request, $numero)
    {
        if ($request->input('filtro') === 'possibili') {
            $p = CategoriaPatente::whereDoesntHave('patenti', function ($query) use ($numero): void {
                $query->where('patenti_categorie.numero_patente', '=', $numero);
            })->get();
        } else {
            $p = Patente::where('numero_patente', $numero)->with('categorie')->first();
        }

        return response()->json($p);
    }

    /**
     * Ritorna tutte le persone siano che hanno la patente sia che non la hanno.
     *
     **/
    public function persone(Request $request)
    {
        $term = $request->term;
        $persone = ViewClientiConSenzaPatente::where('nome', 'LIKE', "$term%")
            ->orwhere('cognome', 'LIKE', "$term%")
            ->orWhere('nominativo', 'LIKE', "$term%")
            // ->orderBy("cliente_con_patente")
            ->take(50)
            ->get();

        $persone->map(function (ViewClientiConSenzaPatente $persona): ViewClientiConSenzaPatente {
            if ($persona->cliente_con_patente !== null) {
                $persona['value'] = "$persona->nome  $persona->cognome (".$persona->cliente_con_patente.')';
            } else {
                $persona['value'] = "$persona->nome  $persona->cognome";
            }

            return $persona;
        });

        return $persone;
    }

    /**
     * Ritorna le persone che non hanno la patente.
     *
     **/
    public function personeSenzaPatente(Request $request)
    {
        $term = $request->term;
        $persone = ViewClientiConSenzaPatente::SenzaPatente()
            ->where(function ($query) use ($term): void {
                $query->where('nome', 'LIKE', "$term%")
                    ->orWhere('cognome', 'LIKE', "$term%")
                    ->orWhere('nominativo', 'LIKE', "$term%");

            })
            ->take(50)
            ->get();

        $persone->map(function (ViewClientiConSenzaPatente $persona): void {
            $persona['value'] = "($persona->data_nascita) $persona->nome  $persona->cognome";
        });

        return $persone;
    }

    /**
     * Aggiorna i dati di una patente
     *
     * @param  string  $numero  : numero della patente
     *                          {
     *                          persona_id: null,
     *                          numero_patente: null,
     *                          rilasciata_dal :null,
     *                          data_rilascio_patente : null,
     *                          data_scadenza_patente : null,
     *                          note : null,
     *                          stato: enum ('commisione', null)
     *                          categorie: [  // array delle nuove categorie assegnate alla patente
     *                          {
     *                          categoria:"A"
     *                          id:4
     *                          pivot:{
     *                          data_rilascio:"2018-10-03"
     *                          data_scadenza:"2018-10-10"
     *                          }
     *                          },
     *                          ....
     *                          ],
     *                          'cqc' :[
     *                          { 'id': int,
     *                          pivot : {'data_rilascio': date
     *                          'data_scadenza': date
     *                          }
     *                          }
     *                          ]
     *                          },
     *
     * @author Davide Neri
     **/
    public function update(Request $request, $numero)
    {
        $body = json_decode($request->getContent(), true);

        $patente = Patente::find($numero);
        $patente->persona_id = $body['persona_id'];
        $patente->numero_patente = $body['numero_patente'];
        $patente->rilasciata_dal = $body['rilasciata_dal'];
        $patente->data_rilascio_patente = $body['data_rilascio_patente'];
        $patente->data_scadenza_patente = $body['data_scadenza_patente'];
        $patente->note = $body['note'] === '' ? null : $body['note'];
        $patente->stato = $body['stato'] === 'null' ? null : $body['stato'];
        $patente->save();
        $categorie = $body['categorie'];
        // from  [ {categoria:"A", id: 4, pivot: { data_rilascio:"2018-10-03", data_scadenza:"2018-10-10" }}, ...]
        // to    [ id => ['data_rilascio =>date, 'data_scadenza'=>date], id2=>[] ]
        $categorie_cqc = collect();
        foreach ($categorie as $value) {
            $categorie_cqc->put($value['id'], []);
            // , array('data_rilascio'=> $value['pivot']['data_rilascio'],
            // 'data_scadenza'=> $value['pivot']['data_scadenza']));
        }

        $cqc = $body['cqc'];
        collect();
        foreach ($cqc as $value) {
            $categorie_cqc->put($value['id'], ['data_rilascio' => $value['pivot']['data_rilascio'],
                'data_scadenza' => $value['pivot']['data_scadenza']]);
        }
        $res = $patente->categorie()->sync($categorie_cqc);

        if ($res) {
            return response()->json(['err' => 0, 'msg' => "Patente $patente->numero_patente aggiornata correttamente"]);
        }

        return response()->json(['err' => 1, 'msg' => "Errore. Patente $patente->numero_patente non aggiornata"]);

    }

    public function rilasciata()
    {
        $rilasciata = Patente::select('rilasciata_dal')->distinct()->get();

        return response()->json($rilasciata);
    }

    /**
     * Crea una nuova patente
     *
     * {
     *  "persona_id":int ,
     *  "data_rilascio_patente":YYYY-MM-GG
     *  "data_scadenza_patente": YYYY-MM-GG ,
     *  "rilasciata_dal": string,
     *  "numero_patente": string,
     *  "note": string,
     *  "stato": enum ('commissione', 'null')
     *  "categorie":[
     *    {
     *       "id":int,
     *       "categoria": string
     *    },
     *   'cqc' :[
     *        { 'id': int,
     *          'data_rilascio': date
     *          'data_scadenza': date
     *
     *      }
     *   ]
     *  ...
     *  ]
     * }
     *
     * @return string
     *                {
     *                'err': 0 | 1,
     *                "msg" : String   // mmessaggio riassuntivo dell'operaione efffetuata dal server
     *                "data": Object   // ot er data
     *                }
     *
     * @author Davide Neri
     **/
    public function create(Request $request)
    {
        $body = json_decode($request->getContent(), true);
        $patente = new Patente;
        $patente->persona_id = $body['persona_id'];
        $patente->numero_patente = $body['numero_patente'];
        $patente->data_rilascio_patente = $body['data_rilascio_patente'];
        $patente->data_scadenza_patente = $body['data_scadenza_patente'];
        $patente->rilasciata_dal = 'test';
        $patente->note = $body['note'];
        $patente->stato = $body['stato'] === 'null' ? null : $body['stato'];

        if ($patente->save()) {
            $p = Patente::find($body['numero_patente']);
            $nuovecategorie = $body['categorie'];
            foreach ($nuovecategorie as $categoria) {
                $p->categorie()->attach($categoria['id']);
            }
            $nuoveCQC = $body['cqc'];
            foreach ($nuoveCQC as $cqc) {
                $p->categorie()->attach([$cqc['id'] => ['numero_patente' => $body['numero_patente'],
                    'data_rilascio' => $cqc['data_rilascio'],
                    'data_scadenza' => $cqc['data_scadenza'],
                ],
                ]);
            }

            return response()->json(
                ['err' => 0,
                    'msg' => 'Patente '.$p->numero_patente.' inserita correttamente',
                    'data' => [
                        'urlPatente' => route('patente.modifica', ['id' => $p->numero_patente]),
                        'urlInserimento' => route('patente.inserimento'),
                    ],
                ], 201);
        }

        return response()->json(['err' => 1, 'msg' => 'Errore nella creazione della patente']);
    }
}
