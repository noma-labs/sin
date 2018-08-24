<?php

namespace App\Biblioteca\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Response;
use Carbon;

use App\Biblioteca\Models\Libro as Libro;
use App\Biblioteca\Models\Autore as Autore;
use App\Biblioteca\Models\Editore as Editore;
// use App\Biblioteca\Models\Cliente as Cliente;
use App\Biblioteca\Models\ViewCollocazione as ViewCollocazione;

use App\Core\Controllers\BaseController as CoreBaseController;
class LibriAutocomplete extends CoreBaseController
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
       $persone = Cliente::where("nominativo", "LIKE", "$term%")->orderBy("nominativo")->get();
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



}
