<?php
namespace App\Patente\Controllers;
use App\Patente\Models\Patente;
use App\Patente\Models\CategoriaPatente;
use App\Nomadelfia\Models\Persona;
use Illuminate\Http\Request;

use App\Core\Controllers\BaseController as CoreBaseController;
use Validator;


class ApiController extends CoreBaseController
{
    /**
     * Ritorna il dettaglio di una patente
     *
     * @param string  $numero nuero della patente
     * @param Request $request
     *
     * @return json $Patente 
     * 
     * @author Davide Neri
     */
    public function patente(Request $request,$numero)
    {
        $p = Patente::where("numero_patente",$numero)->with("categorie")->first();
        return response()->json($p);
    }

    /**
     * Ritorna tutte le categorie
     *
     * @param Request $request
     *
     * @return json $CategoriaPatente
     * 
     * @author Davide Neri
     */
    public function categorie(Request $request)
    {
        $categorie = CategoriaPatente::orderby("categoria")->get();
        return response()->json($categorie);
    }

     /**
     * Ritorna tutte le restrizioni 
     *
     * @param Request $request
     *
     * @return json $Restrizione
     * 
     * @author Davide Neri
     */
    public function restrizioni(Request $request)
    {
        $categorie = CategoriaPatente::orderby("categoria")->get();
        return response()->json($categorie);
    }


    /**
	* Ritorna solo le categorie associate a una patente.
	*    /?filtro=possibili : ritorna le categorie non ancora assegnate alla patente 
    * @param string $numero  numeor della patente

    * @return array  $Patente
    
	* @author Davide Neri
	**/
    public function patenteCategorie(Request $request,$numero)
    {
        if($request->input('filtro') == "possibili")
            $p = CategoriaPatente::whereDoesntHave('patenti', function($query) use ($numero){
                $query->where('patenti_categorie.numero_patente', '=', $numero);
                })->get();
        else
            $p = Patente::where("numero_patente",$numero)->with("categorie")->first();
    

        return response()->json($p);
    }

    public function create(Request $request)
    {
       $body = json_decode($request->getContent(), true);
        // dd($newpatente);
        $patente = new Patente();
        $patente->persona_id = $body['persona_id'];
        $patente->numero_patente = $body['numero_patente'];
        $patente->data_nascita = $body['data_nascita'];
        $patente->luogo_nascita = $body['luogo_nascita'];
        $patente->data_rilascio_patente = $body['data_rilascio_patente'];
        $patente->data_scadenza_patente = $body['data_scadenza_patente'];
        $patente->rilasciata_dal = $body['rilasciata_dal'];
        $patente->note = $body['note'];

        if($patente->save()){
            $nuovecategoria = $body['categorie_patente'];
            foreach ($nuovecategoria as $categoria){
                 $patente->categorie()->attach([$categoria['categoria']['id'] => 
                                                    ['numero_patente' =>$body['numero_patente'],
                                                    'data_rilascio' => $categoria['data_rilascio'],
                                                    'data_scadenza'=>$categoria['data_scadenza']
                                                    
                                                    ]
                                                ]);
            }
            return response()->json("ok"); // array                                    
        }

        return response()->json("error"); // array   

      
        // "persona_id": null,
        // "data_nascita": "2018-09-05",
        // "luogo_nascita": null,
        // "data_rilascio_patente": "2018-09-07",
        // "data_scadenza_patente": "2018-09-07",
        // "rilasciata_dal": null,
        // "numero_patente": "ddd",
        // "note": null,
        // "categorie_patente":[
        //    {"categoria":
        //      {"id":4
        //      ,"categoria":"A"
        //      ,"descrizione":"CLASSIFICATA ANCHE COME PATENTE A3, È CONSEGUIBILE A DIVERSE ETÀ E CON DIFFERENTI MODALITÀ"
        //      ,"note":""
    //          }
        //     ,"data_rilascio":"2018-09-05"
        //     ,"data_scadenza":"2018-09-05"
        //     ,"restrizioni":null
    //         },
        //    {"categoria":
        //        {"id":6
        //       ,"categoria":"B"
        //       ,"descrizione":"ETÀ MINIMA RICHIESTA: 18 ANNI."
        //       ,"note":""}
        //  ,"data_rilascio":"2018-09-05"
        //  ,"data_scadenza":"2018-09-05"
        //  ,"restrizioni":null}]
        //     }
        // }
    }

   

}