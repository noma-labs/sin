<?php
namespace App\Nomadelfia\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;

use Illuminate\Database\QueryException;

use Illuminate\Http\Request;

use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Models\Categoria;
use App\Nomadelfia\Models\Posizione;
use App\Nomadelfia\Models\Famiglia;
use App\Anagrafe\Models\Provincia;
use App\Nomadelfia\Models\GruppoFamiliare;
use App\Nomadelfia\Models\Azienda;
use App\Nomadelfia\Models\Incarico;

use Validator;

class PersoneController extends CoreBaseController
{

  
  public function view(){
    $persone =  Persona::orderBy("nominativo","ASC")->get();
    return view('nomadelfia.persone.index',compact('persone'));
  }

  public function show($idPersona){
    $persona = Persona::findOrFail($idPersona);
    return view("nomadelfia.persone.show",compact('persona'));
  }

  public function modificaDatiAnagrafici($idPersona){
    $persona = Persona::findOrFail($idPersona);
    return view('nomadelfia.persone.edit_anagrafica',compact('persona'));
  }
  
  public function modificaDatiAnagraficiConfirm(Request $request, $idPersona){
    $validatedData = $request->validate([
      "nome" => "required",
      "cognome" => "required",
      "datanascita" => "required",
      "luogonascita" => "required",
      "sesso" => "required",
    ],[
      "nome.required" => "Il nome è obbligatorie",
      "cognome.required" => "Il cognome è obbligatorio",
      "datanascita.required" => "La data di nascita è obbligatoria",
      "luogonascita.required" => "Il luogo di nascita è obbligatorio",
      "sesso.required" => "Il sesso è obbligatorio",
    ]);
    $persona = Persona::findOrFail($idPersona);
    $persona->nome = $request->nome;
    $persona->cognome = $request->cognome;
    $persona->data_nascita = $request->datanascita;
    $persona->provincia_nascita = $request->luogonascita;
    $persona->sesso = $request->sesso;
    if($persona->save())
      return redirect()->route('nomadelfia.persone.dettaglio',['idPersona' =>$idPersona])->withSuccess("Dati anagrafici di $persona->nominativo aggiornati correttamente. ");
    else    
    return redirect()->route('nomadelfia.persone.dettaglio',['idPersona' =>$idPersona])->withSError("Errore dureante l'aggiornamente dei dati anagrafici di $persona->nominativo.");


  }

  public function modificaNominativo($idPersona){
    $persona = Persona::findOrFail($idPersona);
    return view('nomadelfia.persone.edit_nominativo',compact('persona'));
  }

  /**
   * Modifica il nominativo esistente di una persona.
   * 
   * @author Davide Neri
   */
  public function modificaNominativoConfirm(Request $request,$idPersona){
    $validatedData = $request->validate([
      "nominativo" => "required|unique:db_nomadelfia.persone,nominativo",
    ],[
      "nominativo.required" => "Il nominativo è obbligatorio",
      "nominativo.unique" => "Il nominativo $request->nominativo assegnato ad un'altra persona.",
    ]);
    $persona = Persona::findOrFail($idPersona);
    $persona->nominativo = $request->nominativo;
    if($persona->save())
      return redirect()->route('nomadelfia.persone.dettaglio', ['idPersona' => $idPersona])->withSucces("Nominativo  aggiornato con suceesso");
    else
      return redirect()->route('nomadelfia.persone.dettaglio', ['idPersona' => $idPersona])->withError("Errore. Il nominativo non è stato aggiornato.");
    }

   /**
   * Assegna un nuovo nominativo e salva il nominativo attuale nello storico dei nominativi.
   * 
   * @author Davide Neri
   */
    public function assegnaNominativoConfirm(Request $request,$idPersona){
      $validatedData = $request->validate([
        "nuovonominativo" => "required|unique:db_nomadelfia.persone,nominativo",
      ],[
        "nuovonominativorequired" => "Il nominativo è obbligatorio",
        "nuovonominativounique" => "Il nominativo $request->nominativo assegnato ad un'altra persona.",
      ]);
       $persona = Persona::findOrFail($idPersona);
      $persona->nominativiStorici()->create(['nominativo'=> $persona->nominativo]);
      $persona->nominativo = $request->nuovonominativo;
      if($persona->save())
        return redirect()->route('nomadelfia.persone.dettaglio', ['idPersona' => $idPersona])->withSucces("Nuovo nominativo aggiunto con successo.");
      else
        return redirect()->route('nomadelfia.persone.dettaglio', ['idPersona' => $idPersona])->withError("Errore. Il nominativo non è stato assegnato.");
    }

  public function modificaCategoriaConfirm(Request $request, $idPersona){
    $validatedData = $request->validate(["categoria" => "required",],
                                        ["categoria.required" => "La categoria è obbligatoria",
    ]);
    $persona = Persona::findOrFail($idPersona);
    $persona->categoria_id = $request->categoria;
    if($persona->save())
      return redirect()->route('nomadelfia.persone.dettaglio', ['idPersona' => $idPersona])->withSucces("Stato attuale modificato con successo.");
    else
      return redirect()->route('nomadelfia.persone.dettaglio', ['idPersona' => $idPersona])->withError("Errore: Stato attuale non aggiornato correttamente.");

  }

  public function insertView(){
    return view("nomadelfia.persone.insert_initial");
  }

  public function insertCompletoView(){
    return view("nomadelfia.persone.insert");
  }

  /**
   * Contolla che non ci sia una persona con il nome e cognome.
   * Ritorna la lista delle persone che hanno o il nome o cognome inserito.
   * Se non esistono persone ritorna il form per aggiungere la persona.
   * 
   * @author Davide Neri
   */
  public function insertInitial(Request $request){
    $validatedData = $request->validate([
      "persona" => "required",
      // "nome" => "required",
      // "cognome" => "required",
    ],[
      // "nominativo.required" => "Il nome è obbligatorie",
      // "nome.required" => "Il nome è obbligatorie",
      "persona.required" => "Il cognome è obbligatorio",
    ]);
   
    if ($request->filled('persona')) {
      $personeEsistenti = Persona::where("nominativo","like","%".$request->persona."%")
                                  ->orWhere("nome", "like", "%".$request->persona."%")
                                  ->orWhere("cognome", "like", "%".$request->persona);
      if($personeEsistenti->exists() )
        return view("nomadelfia.persone.insert_existing", compact('personeEsistenti'));
      else
         return redirect(route('nomadelfia.persone.inserimento.completo'))->withSuccess("Nessuna persona presente con nome e cognome inseriti.")->withInput();
    }
  }

  /**
   * Inserisci una persona nel sistema con i dati personali.
   * L'inserimento associa un ID alla persona che è
   * è l'identificativo univoco usato in tutti i sistemi per
   * identificare la persona.
   * 
   * @author Davide Neri
   */
  public function insert(Request $request){

    $validatedData = $request->validate([
        "nominativo" => "required|unique:db_nomadelfia.persone,nominativo", 
        "nome" => "required",
        "cognome" => "required",
        "data_nascita" => "required|date",
        "luogo_nascita" => "required",
        "sesso" => "required",
        "categoria_id" => "required"
      ],[
        "nominativo.required" => "Il nominativo è obbligatorio", 
        'nominativo.unique'=>"IL nominativo inserito esiste già.",
        "nome.required" => "Il nome è obbligatorie",
        "cognome.required" => "Il cognome è obbligatorio",
        "data_nascita.required" => "La data di nascita è obbligatoria",
        "luogo_nascita.required" => "IL luogo di nascita è obbligatorio",
        "sesso.required" => "Il sesso della persona è obbligatorio",
        "categoria_id.required" => "La categoria della persona è obbligatoria",

    ]);
    $_addanother= $request->input('_addanother');  // save and add another libro
    $_addonly   = $request->input('_addonly');     // save only
    try{
      $persona = Persona::create(['nominativo'=>$request->input('nominativo'), 
                                'sesso'=>$request->input('sesso'),
                                'nome'=>$request->input('nome'),
                                "cognome"=>$request->input('cognome'),
                                "provincia_nascita"=>$request->input('luogo_nascita'),
                                'data_nascita'=>$request->input('data_nascita'),
                                'categoria_id' =>$request->input('categoria_id'),
                                'id_arch_pietro'=>0,
                                'id_arch_enrico'=>0,]
                              );
      // $persona->save();
      if($_addanother  && $persona->save())
        return redirect(route('nomadelfia.persone.inserimento'))->withSuccess("Persona $persona->nominativo inserita correttamente.");
      if($_addonly)
        return redirect()->route('nomadelfia.persone.dettaglio', [$persona->id])->withSuccess("Persona $persona->nominativo inserita correttamente.");
      
    }
    catch (Illuminate\Database\QueryException $e){
        $error_code = $e->errorInfo[1];
        if($error_code == 1062){
            return redirect(route('nomadelfia.persone.inserimento'))->withError('Persona già esistente con il nominativo.');
        }
        return redirect(route('nomadelfia.persone.inserimento'))->withError("Errore sconosciuto.");
    }
    // $persona->posizioni()->attach($request->input('posizione'), ['data_inizio' => $request->input('inizio')]);
    // $persona->famiglie()->attach($request->input('famiglia'), ['nucleo_famigliare_id' => $request->input('nucleo')]);
    // $persona->gruppi()->attach($request->input('gruppo'), ['data_entrata_gruppo' => $request->input('data_gruppo')]);
    // if ($request->input('azienda') != ''){
    //   $persona->aziende()->attach($request->input('azienda'), ['data_inizio_azienda' => $request->input('data_lavoro')]);
    // }

    // if ($request->input('incarico') != ''){
    //   $persona->incarichi()->attach($request->input('incarico'), ['data_inizio' => $request->input('data_incarico')]);
    // }
    // return redirect(route('nomadelfia.persone.inserimento'))->withSuccess('Iserimento completato');
  }

  /**
   * Ritorna la view per la modifica della posizione assegnata ad una persona
   * 
   * @author Davide Neri
   */
  public function posizione($idPersona){
    $persona = Persona::findOrFail($idPersona);
    return view("nomadelfia.persone.posizione.show",compact('persona'));
  }

  /**
   * Assegna una nuova posizione ad una persona.
   * 
   * @author Davide Neri
   */
  public function assegnaPosizione(Request $request, $idPersona){ 
    $validatedData = $request->validate([
      "posizione_id" => "required", 
      "data_inizio" => "required|date",
      // "data_fine" => "required|date",
    ],[
      "posizione_id.required" => "La posizione è obbligatorio", 
      'data_inizio.required'=>"La data di inizio della posizione è obbligatoria.",
      // 'data_fine.required'=>"La data fine della posizione è obbligatoria.",
  ]);
    $persona = Persona::findOrFail($idPersona);
    if($persona->posizioneAttuale()) // se 
      $persona->posizioni()->updateExistingPivot($persona->posizioneAttuale()->id, ['stato'=>'0','data_fine'=>($request->data_fine ? $request->data_fine: $request->data_inizio)]);
    $persona->posizioni()->attach($request->posizione_id, ['stato'=>'1','data_inizio'=>$request->data_inizio]);
    return redirect(route('nomadelfia.persone.dettaglio',[$persona->id]))->withSuccess("Nuova posizione assegnata a $persona->nominativo  con successo.");

  }


  /**
   * Ritorna la view per la modifica dello stato assegnato ad una persona
   * 
   * @author Davide Neri
   */
  public function stato($idPersona){
    $persona = Persona::findOrFail($idPersona);
    return view("nomadelfia.persone.stato.show",compact('persona'));
  }

   /**
   * Assegna un nuovo stato ad una persona.
   * 
   * @author Davide Neri
   */
  public function assegnaStato(Request $request, $idPersona){ 
    $validatedData = $request->validate([
      // "data_fine" => "required|date", 
      "stato_id" => "required", 
      "data_inizio" => "required|date",
    ],[
      "stato_id.required" => "Lo stato è obbligatorio", 
      'data_inizio.required'=>"La data iniziale dello stato è obbligatoria.",
      // 'data_fine.required'=>"La data dell fine dello è obbligatoria.",
  ]);
    $persona = Persona::findOrFail($idPersona);
    if($persona->statoAttuale()) 
      $persona->stati()->updateExistingPivot($persona->statoAttuale()->id, ['stato'=>'0','data_fine'=>($request->data_fine ? $request->data_fine: $request->data_inizio)]);
    $persona->stati()->attach($request->stato_id, ['stato'=>'1','data_inizio'=>$request->data_inizio]);
    return redirect(route('nomadelfia.persone.dettaglio',[$persona->id]))->withSuccess("Stato assegnato a $persona->nominativo con successo");
  }

  /**
   * Ritorna la view per la modifica del gruppo familiare
   * 
   * @author Davide Neri
   */
  public function gruppoFamiliare($idPersona){
    $persona = Persona::findOrFail($idPersona);
    return view("nomadelfia.persone.gruppofamiliare.show",compact('persona'));
  }

   /**
   * Assegna un nuovo gruppo familiare ad una persona
   * 
   * @author Davide Neri
   */
  public function assegnaGruppofamiliare(Request $request, $idPersona){ 
    $validatedData = $request->validate([
      "gruppo_id" => "required", 
      // "data_uscita" => "required|date",
      "data_entrata" => "required|date",
    ],[
      "gruppo_id.required" => "Il nuovo gruppo è obbligatorio", 
      'data_entrata.required'=>"La data di entrata nel gruppo familiare è obbligatoria.",
      // 'data_uscita.required'=>"La data di uscita nel gruppo familiare è obbligatoria.",
  ]);
    $persona = Persona::findOrFail($idPersona);
    if($persona->gruppofamiliareAttuale()) // se ha già uno stato attuale aggiorna lo stato attuale
      $persona->gruppifamiliari()->updateExistingPivot($persona->gruppofamiliareAttuale()->id, ['stato'=>'0',
                                                                    'data_uscita_gruppo'=>($request->data_uscita ? $request->data_uscita: $request->data_entrata)]);
    $persona->gruppifamiliari()->attach($request->gruppo_id, ['stato'=>'1','data_entrata_gruppo'=>$request->data_entrata]);
    return redirect(route('nomadelfia.persone.dettaglio',[$persona->id]))->withSuccess("$persona->nominativo assegnato al gruppo familiare con successo");
  }

  

  public function modificaGruppoFamiliare(Request $request, $idPersona){ 
    $validatedData = $request->validate([
      "nuovogruppo" => "required", 
      "datacambiogruppo" => "required|date",
    ],[
      "nuovogruppo.required" => "Il nuovo gruppo è obbligatorio", 
      'datacambiogruppo.required'=>"La data del cambio di gruppo è obbligatoria.",
  ]);
     $persona = Persona::findOrFail($idPersona);
     
          $data = $request->datacambiogruppo;
          $idnuovogruppo =  $request->nuovogruppo;
         $persona->cambiaGruppoFamiliare($persona->gruppofamiliareAttuale()->id, $data, $idnuovogruppo, $data);

     return redirect(route('nomadelfia.persone.dettaglio',[$persona->id]))->withSuccess("Spostamento in un gruppo familiare eeguito con successo");

  }

}
