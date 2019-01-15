<?php
namespace App\Nomadelfia\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use Exception;


use Illuminate\Http\Request;

use App\Nomadelfia\Models\Famiglia;

class FamiglieController extends CoreBaseController
{
 
  /**
  * Ritorna la view di gestione delle famiglie
  * @author Davide Neri
  **/
  public function view(){
    $capifamiglieMaschio = Famiglia::onlyCapoFamiglia()->maschio();
    $capifamiglieFemmina = Famiglia::onlyCapoFamiglia()->femmina();

    $singleMaschio = Famiglia::onlySingle()->maschio();
    $singleFemmine = Famiglia::onlySingle()->femmina();

    return view('nomadelfia.famiglie.index',compact('capifamiglieMaschio','capifamiglieFemmina','singleMaschio','singleFemmine'));
  }

  /**
  * Ritorna la view di gestione din una singola famiglia
  * @author Davide Neri
  **/
  public function show(Request $request, $id){ 
    $famiglia = Famiglia::findorfail($id);
    return view('nomadelfia.famiglie.show',compact('famiglia'));
  }

  public function assegnaGruppoFamiliare(Request $request, $id){ 
    $validatedData = $request->validate([
      "nuovogruppo" => "required", 
      "datacambiogruppo" => "required|date",
    ],[
      "nuovogruppo.required" => "Il nuovo gruppo è obbligatorio", 
      'datacambiogruppo.required'=>"La data del cambio di gruppo è obbligatoria.",
  ]);
    $famiglia = Famiglia::findorfail($id);
    $famiglia->assegnaFamigliaANuovoGruppoFamiliare($famiglia->gruppoFamiliareAttuale(), $request->datacambiogruppo,
                                                    $request->nuovogruppo, $request->datacambiogruppo);
    return redirect(route('nomadelifa.famiglia.dettaglio',['id'=>$id]))->withSuccess("Famiglia spostata nel ngruppo familiare con successo");
  }

  public function assegnaComponente(Request $request, $id){ 
    $validatedData = $request->validate([
      "persona_id" => "required", 
      "posizione" => "required",
      "stato" => "required",
      "data_entrata" => "required|date"
    ],[
      "persona_id.required" => "La persona è obbligatoria.", 
      "stato.required" => "Lo stato della persona è obbligatoria.", 
      'posizione.required'=>"La posizione nella famiglia è obbligatoria.",
      'data_entrata.required'=>"La data di entrata nella famiglia è obbligatoria.",
      'data_entrata.date'=>"La data del cambio di gruppo non è una data corretta.",

  ]);
    $famiglia = Famiglia::findorfail($id);
    try{
       $famiglia->componenti()->attach($request->persona_id,['stato'=>$request->stato,'posizione_famiglia'=>$request->posizione, 
                                                          'data_entrata'=>$request->data_entrata,'note'=>$request->note]);
      return  redirect(route('nomadelifa.famiglia.dettaglio',['id'=>$id]))->withSuccess("Componente aggiunto alla famiglia con successo");
    }catch (Exception $e){
      return redirect(route('nomadelifa.famiglia.dettaglio',['id'=>$id]))->withError("Errore. Nessun componente aggiunto alla famiglia.");
    }
  }

}
