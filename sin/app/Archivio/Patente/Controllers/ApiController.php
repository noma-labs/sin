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
     * Dato il numero di una patente, ritorna la patente e le categorie associate
     *
     * @param string  $numero  della patente
     * @param Request $request
     *
     * @return json $Patente 
     * {
     * "persona_id": number,
     * "numero_patente": string,
     * "rilasciata_dal": string,
     * "data_rilascio_patente": date  "GG-MM-YYYY",
     * "data_scadenza_patente":date "GG-MM-YYYY",
     * "stato": enum ('commissione',NULL),
     * "note": string,
     * "categorie":[
     *      {"id": number,
     *       "categoria": string,
     *       "descrizione": string,
     *       "note":"string,
     *       "pivot":{
     *              "numero_patente": string,
     *              "categoria_patente_id":string,
     *              "data_rilascio":date ("2016-01-07")
     *              "data_scadenza": date ("2021-01-05)
     *        }
     *       }
     * ]}
     * @author Davide Neri
     */
    public function patente(Request $request,$numero)
    {
        $p = Patente::where("numero_patente",$numero)->with(["categorie"])->first();
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
   
    /**
	* Ritorna le persone che hanno l'età per prendere una patente.
	* @param String $term: Nome, cognome o nominativo della persona
	* @author Davide Neri
	**/
	public function persone(Request $request){
        
        $term = $request->term;
		// $persone = Persona::where("nominativo", "LIKE", "$term%")->orderBy("nominativo")->get();
		// $pesrone = DatiPersonali::where('nome',"LIKE","$term%")->orWhere('cognome',"LIKE","$term%")->get();
		$persone = Persona::with("datipersonali")
                    ->where("nominativo","LIKE","$term%");
                    // ->daEta(16);
		return $persone->get();
    }
    
    /**
    * Aggiorna i dati di una patente
    *
    * @param String $numero: numero della patente
    * @param Json  $patente: patente con i dati aggiornati
    * {
    *  persona_id: null,
    *  numero_patente: null,
    *  rilasciata_dal :null,
    *  data_rilascio_patente : null,
    *  data_scadenza_patente : null,
    *  note : null,
    *  stato: enum ('commisione', null)
    *  categorie: [  // array delle nuove categorie assegnate alla patente
    *      { 
    *         categoria:"A"
    *         id:4
    *         pivot:{
    *               data_rilascio:"2018-10-03"
    *               data_scadenza:"2018-10-10"
    *           }
    *        },
    *     ....
    *  ],          	
    *               },
    * @author Davide Neri
	**/
    public function update(Request $request, $numero){
        $body = json_decode($request->getContent(), true);

        $patente = Patente::find($numero);
        $patente->persona_id = $body['persona_id'];
        $patente->numero_patente = $body['numero_patente'];
        $patente->rilasciata_dal = $body['rilasciata_dal'];
        $patente->data_rilascio_patente = $body['data_rilascio_patente'];
        $patente->data_scadenza_patente = $body['data_scadenza_patente'];
        $patente->note = $body['note'];
        $patente->stato =  $body['stato'] == "null" ?  Null: $body['stato'];
        $patente->save();
        $categorie = $body['categorie'];
        // from  {  categoria:"A", id:4, pivot:{ data_rilascio:"2018-10-03", data_scadenza:"2018-10-10" }
        // to    [id 1=>['data_rilascio =>date, 'data_scadenza'=>date], id2=>[] 
        $categoria_formatted = collect();
        foreach ($categorie as $key => $value){
            $categoria_formatted->put($value['id'], array('data_rilascio'=> $value['pivot']['data_rilascio'],
                                            'data_scadenza'=> $value['pivot']['data_scadenza']));
        }
        $res = $patente->categorie()->sync($categoria_formatted);
        if($res)
         return response()->json(["err"=>0, "msg"=> "Patente $patente->numero_patente aggiornata correttamente"]); 
        else
            return response()->json(["err"=>1, "msg"=> "Errore. Patente $patente->numero_patente non aggiornata"]); 


    }


     /**
    * Crea una nuova patente
    *
    * @param Json  $patente: patente con i dati aggiornati
    * { 
    *  "persona_id":int ,
    *  "data_rilascio_patente":YYYY-MM-GG 
    *  "data_scadenza_patente": YYYY-MM-GG ,
    *  "rilasciata_dal": string,
    *  "numero_patente": string,
    *  "note": string,
    *  "stato": enu ('commissione', 'null')
    *  "categorie":[  
    *    {  
    *       "categoria":{  
    *          "id":int,
    *          "categoria":string
    *       },
    *       "data_rilascio":YYYY-MM-GG 
    *       "data_scadenza":YYYY-MM-GG 
    *    },
    *  ...
    *  ]
    * }
    * @author Davide Neri
	**/
    public function create(Request $request)
    {
        $body = json_decode($request->getContent(), true);
        $patente = new Patente();
        $patente->persona_id = $body['persona_id'];
        $patente->numero_patente = $body['numero_patente'];
        $patente->data_rilascio_patente = $body['data_rilascio_patente'];
        $patente->data_scadenza_patente = $body['data_scadenza_patente'];
        $patente->rilasciata_dal = $body['rilasciata_dal'];
        $patente->note = $body['note'];
        $patente->stato = $body['stato'] == "null" ?  Null: $body['stato'];

        if($patente->save()){
            $nuovecategorie = $body['categorie'];
            foreach ($nuovecategorie as $categoria){
                 $cat = $categoria['categoria'];
                 $patente->categorie()->attach([$cat['id'] => [
                                                     'numero_patente' =>$body['numero_patente'],
                                                    'data_rilascio' => $categoria['data_rilascio'],
                                                    'data_scadenza'=>$categoria['data_scadenza']
                                                    ]
                                                ]);
            }
            return response()->json(
                    ["err"=>0, 
                    "msg"=> "Patente $patente->numero_patente inserita correttamente"]
                    ,201); 
        }
        return response()->json(["err"=>1, "msg"=>"Errore nella creazione della patente"]); 
    }

   

}