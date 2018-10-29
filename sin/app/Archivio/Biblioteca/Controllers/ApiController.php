<?php
namespace App\Biblioteca\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Response;
use Carbon;

use App\Core\Controllers\BaseController as CoreBaseController;

use App\Biblioteca\Models\Libro as Libro;
use App\Biblioteca\Models\ViewClientiBiblioteca;
use App\Biblioteca\Models\Autore as Autore;
use App\Biblioteca\Models\Editore as Editore;
use App\Biblioteca\Models\ViewCollocazione as ViewCollocazione;


class ApiController extends CoreBaseController
{
  
  public function autocompleteLibro(Request $request){
    $term = $request->term;
    $libri = Libro::where("titolo","like","%$term%")->orWhere('collocaione', "like","$term%");
    if($libri->count() > 0){
      foreach ($libri as $libro)
      {
          $results[] = ['value'=>$libro->id, 'label'=>$libro->collocazione];
      }
      return response()->json($results);
    }else {
      return response()->json(['value'=>"", 'label'=> "libro non trovato"]);
    }

  }

 public function autocompleteCliente(Request $request){
   $term = $request->term;
   if($term)
      $persone = ViewClientiBiblioteca::where("nominativo", "LIKE", "$term%")->orderBy("nominativo")->get();
   if($persone->count() > 0){
     foreach ($persone as $persona)
     {
        $year =   Carbon\Carbon::createFromFormat('Y-m-d', $persona->data_nascita)->year;
        $results[] = ['value'=>$persona->id, 'label'=>"$persona->nominativo ($year)" ];
     }
     return response()->json($results);
   }else {
     return response()->json(['value'=>"", 'label'=> "persona non esiste"]);
   }

 }

 public function autocompleteCollocazione(Request $request){
   // ?term=q       => return all the lettere that start with "q"
   // ?lettere=XXX  => return the numeri (liberi, asseganti, nuovo numoro) for the selecte lettere
   // ?lettere=XXX&soloassegnati=true => return onle numeri assseganto for the letters
    if($request->has('term')){
      $CollocazioneLettere = ViewCollocazione::lettere()
                           ->where("lettere", "LIKE", $request->input('term').'%')
                           ->get();
     $results[]  = ['value'=>"null", 'label'=>"SENZA COLLOCAZIONE"];
      foreach ($CollocazioneLettere as $lettere)
          $results[] = ['value'=>$lettere->lettere, 'label'=>$lettere->lettere];//'id' => $query->id, 'value' => $query->first_name.' '.$query->last_name ];
      return response()->json($results);
   }
   if($request->has('lettere')){
       $lettere =  $request->input('lettere');
       $max = ViewCollocazione::MaxForLettere($lettere); // max numero associated with the lettere
       $numeri = ViewCollocazione::numeri($lettere)->get()->pluck("numeri")->toArray();
       $arr2 = range(1,$max);
       $res = array_diff($arr2,$numeri);

       $result =["numeriAssegnati"=> ($request->input('assegnati',"true") === "true") ? $numeri : null, //'Off';  // $numeri,
                 "numeriMancanti" => ($request->input('mancanti',"true") === "true") ? $res : null,
                 "numeroNuovo"=>     ($request->input('nuovo',"true") === "true") ? $max + 1 : null
               ];
       return response()->json($result);
   }
 }

   public function autocompleteAutori(){
     $term = Input::get('term');
       $autori = Autore::where("autore", "LIKE", '%'.$term.'%')->orderBy("autore")->take(50)->get();
       $results = array();
       foreach ($autori as $autore)
           $results[] = ['value'=>$autore->id, 'label'=>$autore->autore];
       return response()->json($results);
   }

   public function autocompleteEditori(){
      $term = Input::get('term');
       $editori = Editore::where("Editore", "LIKE", '%'.$term.'%')->orderBy("editore")->take(50)->get();
       $results = array();
       foreach ($editori as $editore)
           $results[] = ['value'=>$editore->id, 'label'=>$editore->editore];
       return response()->json($results);
   }

   public function autocompleteTitolo()
   {
     $term = Input::get('term');
     $libri = Libro::withTrashed()->select("titolo")->where("titolo", "LIKE", $term.'%')->groupBy('titolo')->take(50)->get();
     $results = array();
     foreach ($libri as $libro)
         $results[] = ['value'=>$libro->titolo,'label'=>$libro->titolo];
     return response()->json($results);
   }
   /**
    * Insert a new autore,
    * @return: 
    * @author: Davide Neri
    */
  public function postAutore(Request $request){
      if ($request->filled('nome')) {
        $nome = $request->input('nome');
        $autore = Autore::where('autore', $nome)->first();
        if (!$autore) {
          $autore = Autore::create(['autore' => $nome]);
          $msg = "Autore $autore->autore inserito correttamente";
          return response()->json(['err'=>0, 'msg' => $msg]);
        }else
          $msg = "Autore $autore->autore esiste già.";
          return response()->json(['err'=>1, 'msg' => $msg]);
      }else {
        return response()->json([
                      'err'=>1,
                      'error' => "l'autore non è stato passato correttamente"
                    ], 400);
      };
  }
  /**
   * Inserisce un nuovo editore.
   * 
   * @param String nome: nome dell'editore.
   * @return json 
   * {
   * "err": 0,// 1
   * "data": {
   *     "id": 3880,
   *     "editore": "DIDO-EDITORE-2",
   *     "created_at": "2018-10-29 11:36:28",
   *     "updated_at": "2018-10-29 11:36:28",
   *     "tipedi": "S"
   *  },
   *  "msg": "Editore DIDO-EDITORE-2 esiste già."
   * }
   */

  public function postEditore(Request $request){
      if ($request->filled('nome')) {
        $nome = $request->input('nome');
        $editore = Editore::where('editore', $nome)->first();
        if (!$editore) {
          $editore = Editore::create(['editore' => $nome]);
          $msg = "Editore $editore->editore inserito correttamente";
          return response()->json(['err'=>0, 'data'=> $editore, 'msg' => $msg]);
        }else
          $msg = "Editore $editore->editore esiste già.";
         return response()->json(['err'=>1, 'data'=> $editore, 'msg' => $msg]);
      }else {
        return response()->json([
                      'err'=>1,
                      'msg'=> "l'editore non è stato passato correttamente"
                    ], 400); // Status code here
      };
  }


}
