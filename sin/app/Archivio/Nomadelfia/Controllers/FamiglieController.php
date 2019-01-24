<?php
namespace App\Nomadelfia\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use Exception;


use Illuminate\Http\Request;

use App\Nomadelfia\Models\Famiglia;
use App\Nomadelfia\Models\Persona;


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

    $famiglieNoComponenti = Famiglia::doesntHave("componenti");

    return view('nomadelfia.famiglie.index',compact('capifamiglieMaschio','capifamiglieFemmina','singleMaschio','singleFemmine','famiglieNoComponenti'));
  }

  /**
  * Ritorna la view di gestione din una singola famiglia
  * @author Davide Neri
  **/
  public function show(Request $request, $id){ 
    $famiglia = Famiglia::findorfail($id);
    return view('nomadelfia.famiglie.show',compact('famiglia'));
  }


  /**
  * Ritorna la view per creare una nuova famiglia
  * @author Davide Neri
  **/
  public function create(Request $request){ 
    return view('nomadelfia.famiglie.create');
  }

  /**
  * Crea una nuova famiglia
  * @author Davide Neri
  **/
  public function createConfirm(Request $request){ 
    $validatedData = $request->validate([
      "nome" => "required|unique:db_nomadelfia.famiglie,nome_famiglia", 
      "data_inizio" => "required|date",
      ],[
        "nome.required" => "Il nome della famiglia  è obbligatorio", 
        "nome.unique" => "Il nome della famiglia esiste già", 
        'data_inizio.required'=>"La data di creazione della famiglia è obbligatoria.",
    ]);
    $fam = Famiglia::create(['nome_famiglia'=>$request->nome, 'data_creazione'=>$request->data_inizio]);
    return redirect(route('nomadelfia.famiglia.dettaglio',['id'=>$fam->id]))->withSuccess("Famiglia $fam->nome_famiglia creata con successo");
  }

  /**
  * Sposta la famiglia e tutti i componenti attivi (stato = 1) in un nuovo gruppo familiare.
  *
  * @author Davide Neri
  **/
  public function assegnaGruppoFamiliare(Request $request, $id){ 
    $validatedData = $request->validate([
      "gruppo_id" => "required", 
      "data_cambiogruppo" => "required|date",
    ],[
      "gruppo_id.required" => "Il nuovo gruppo è obbligatorio", 
      'data_cambiogruppo.required'=>"La data del cambio di gruppo è obbligatoria.",
  ]);
    $famiglia = Famiglia::findorfail($id);
    $famiglia->assegnaFamigliaANuovoGruppoFamiliare($famiglia->gruppoFamiliareAttuale(), $request->data_cambiogruppo,
                                                    $request->gruppo_id, $request->data_cambiogruppo);
    return redirect(route('nomadelfia.famiglia.dettaglio',['id'=>$id]))->withSuccess("Famiglia spostata nel gruppo familiare con successo");
  }

  public function assegnaComponente(Request $request, $id){ 
    $validatedData = $request->validate([
      "persona_id" => "required", 
      "posizione" => "required",
      "stato" => "required",
      // "data_entrata" => "required|date"
    ],[
      "persona_id.required" => "La persona è obbligatoria.", 
      "stato.required" => "Lo stato della persona è obbligatoria.", 
      'posizione.required'=>"La posizione nella famiglia è obbligatoria.",
      // 'data_entrata.required'=>"La data di entrata nella famiglia è obbligatoria.",
      'data_entrata.date'=>"La data del cambio di gruppo non è una data corretta.",
  ]);
    $famiglia = Famiglia::findorfail($id);
    $persona = Persona::findorfail($request->persona_id);
    $famiglia_attuale = $persona->famigliaAttuale();
    if($famiglia_attuale != null and $request->stato == "1")
      return redirect(route('nomadelfia.famiglia.dettaglio',['id'=>$id]))->withWarning("Attenzione. $persona->nominativo è già assegnato alla famiglia $famiglia_attuale->nome_famiglia come ". $famiglia_attuale->pivot->posizione_famiglia);

    try{
       $famiglia->componenti()->attach($persona->id,['stato'=>$request->stato,'posizione_famiglia'=>$request->posizione, 
                                                          'data_entrata'=>($request->data_entrata ? $request->data_entrata: $persona->data_nascita),
                                                          'note'=>$request->note]);
      return  redirect(route('nomadelfia.famiglia.dettaglio',['id'=>$id]))->withSuccess("$persona->nominativo aggiunto alla famiglia $famiglia->nome_famiglia con successo");
    }catch (Exception $e){
      return redirect(route('nomadelfia.famiglia.dettaglio',['id'=>$id]))->withError("Errore. Nessun componente aggiunto alla famiglia.");
    }
  }

  public function aggiornaComponente(Request $request, $id){ 
    // dd($request->all());
    $validatedData = $request->validate([
      "persona_id" => "required", 
      "posizione" => "required",
      "stato" => "required",
      "data_entrata" => "date",
      "data_uscita" => "date"
    ],[
      "persona_id.required" => "La persona è obbligatoria.", 
      "stato.required" => "Lo stato della persona è obbligatoria.", 
      'posizione.required'=>"La posizione nella famiglia è obbligatoria.",
      'data_entrata.date'=>"La data di entrana nella famiglia non è una data corretta.",
      'data_uscita.date'=>"La data di uscita dalla famiglia non è una data corretta.",

  ]);
    $famiglia = Famiglia::findorfail($id);
    try{
       $famiglia->componenti()->updateExistingPivot($request->persona_id,['stato'=>$request->stato,'posizione_famiglia'=>$request->posizione, 
                                                          'data_entrata'=>$request->data_entrata,'data_uscita'=>$request->data_uscita,'note'=>$request->note]);
       return  redirect(route('nomadelfia.famiglia.dettaglio',['id'=>$id]))->withSuccess("Componente aggiornato con successo");
    }catch (Exception $e){
      return redirect(route('nomadelfia.famiglia.dettaglio',['id'=>$id]))->withError("Errore. Nessun componente aggiornato alla famiglia.");
    }
  }

}
