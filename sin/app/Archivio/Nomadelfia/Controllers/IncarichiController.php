<?php
namespace App\Nomadelfia\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;

use Illuminate\Http\Request;

use App\Nomadelfia\Models\Azienda;

class IncarichiController extends CoreBaseController
{
  /**
  * view della pagina di gestione delle aziende
  * @author Matteo Neri
  **/
  public function view(){
    $aziende =  Azienda::incarichi()->with('lavoratoriAttuali')->get();
    return view('nomadelfia.incarichi.index',compact('aziende'));
  }

  /**
  * ritorna la view per editare una azienda
  * @param id dell'azienda da editare
  * @author Matteo Neri
  **/
  public function edit($id){
    $azienda = Azienda::findOrFail($id);
    return view('nomadelfia.aziende.edit', compact('azienda'));

  }

  public function  editConfirm(Request $request, $idPersona){
 }

  public function insert(){
  }

  public function insertConfirm(Request $request){ //InsertClientiRequest $request

  }

  public function searchPersona(Request $request){
    $term = $request->term;
    if($term)
       $persone = Persona::where("nominativo", "LIKE", "$term%")->orderBy("nominativo")->get();

    if($persone->count() > 0){
      foreach ($persone as $persona)
      {
          $results[] = ['value'=>$persona->id, 'label'=>$persona->nominativo];
      }
      return response()->json($results);
    }else {
      return response()->json(['value'=>"", 'label'=> "persona non esiste"]);
    }

  }

}
