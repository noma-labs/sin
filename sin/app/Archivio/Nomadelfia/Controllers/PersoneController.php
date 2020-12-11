<?php
namespace App\Nomadelfia\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;

use Illuminate\Database\QueryException;

use Illuminate\Http\Request;

use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Models\Categoria;
use App\Nomadelfia\Models\Posizione;
use App\Nomadelfia\Models\Stato;
use App\Nomadelfia\Models\Famiglia;
use App\Anagrafe\Models\Provincia;
use App\Nomadelfia\Models\GruppoFamiliare;
use App\Nomadelfia\Models\Azienda;
use App\Nomadelfia\Models\Incarico;
use App\Nomadelfia\Models\PopolazioneNomadelfia;
use Carbon\Carbon;

use Validator;

class PersoneController extends CoreBaseController
{

  public function index(){
    $effettivi = PopolazioneNomadelfia::effettivi();
    $postulanti = PopolazioneNomadelfia::postulanti();
    return view("nomadelfia.persone.effettivi", compact("effettivi", "postulanti"));
  }


  public function show($idPersona){
    $persona = Persona::findOrFail($idPersona);
    $categoriaAttuale = $persona->categoriaAttuale();

    $gruppoAttuale = $persona->gruppofamiliareAttuale();
    $famigliaAttuale = $persona->famigliaAttuale();
    return view("nomadelfia.persone.show",compact('persona', 'categoriaAttuale','gruppoAttuale', 'famigliaAttuale'));
  }

  public function decesso(Request $request, $idPersona){
    $validatedData = $request->validate([
      "data_decesso" => "required",
    ],[
      "data_decesso.required" => "La data del decesso è obbligatorio",
    ]);
    $persona = Persona::findOrFail($idPersona);
    $persona->uscitoODeceduto($request->data_decesso, true);
    return redirect()->route('nomadelfia.persone.dettaglio',['idPersona' =>$idPersona])->withSuccess("IL decesso di $persona->nominativo aggiornato correttamente.");
  }

  public function uscita(Request $request, $idPersona){
    $validatedData = $request->validate([
      "data_uscita" => "required",
    ],[
      "data_uscita.required" => "La data del decesso è obbligatorio",
    ]);
    $persona = Persona::findOrFail($idPersona);
    $persona->uscitoODeceduto($request->data_uscita);
    return redirect()->route('nomadelfia.persone.dettaglio',['idPersona' =>$idPersona])->withSuccess("IL decesso di $persona->nominativo aggiornato correttamente.");
  }

  public function modificaDatiAnagrafici($idPersona){
    $persona = Persona::findOrFail($idPersona);
    return view('nomadelfia.persone.edit_anagrafica',compact('persona'));
  }

  
  public function search(){
    return view("nomadelfia.persone.search");
  }

  // Rimuove (soft delete) una persona nel sistema.
  public function rimuovi($idPersona){
    $persona =Persona::findOrFail($idPersona);
    if ($persona->delete()){
      return redirect()->route('nomadelfia.persone')->withSuccess("Persona $persona->nominativo eliminata caon successo");
    }
    return view("nomadelfia.persone")->withError("Impossibile eliminare $persona->nominativo ");
  }

  public function searchPersonaSubmit(Request $request)
    {
      /* $validatedData = $request->validate([
        'nominativo'=>"exists:db_biblioteca.editore,id",
        'nome'=>"exists:db_biblioteca.autore,id",
        'cognome'=>"exists:db_biblioteca.classificazione,id",
        ],[
          'xIdEditore.exists' => 'Editore inserito non esiste.',
          'xIdAutore.exists' => 'Autore inserito non esiste.',
          'xClassificazione.exists' => 'Classificazione inserita non esiste.',
      ]);
 */
        $msgSearch = " ";
        $orderBy = "nominativo";

       if(!$request->except(['_token']))
        return redirect()->route('nomadelfia.persone.ricerca')->withError('Nessun criterio di ricerca selezionato oppure invalido');


       $queryLibri = Persona::sortable()->where(function($q) use ($request, &$msgSearch, &$orderBy){
            if ($request->filled('stato') && $request->stato == "on"){
              // include anche le persone che sono disattivate (stato = 0)
              $stato = $request->stato;
              $msgSearch= $msgSearch." Persone Attive e Disattive, ";
            }else{
              // includi solo persone attive
              $q->where('stato', '=', "1");
              $msgSearch= $msgSearch." Solo persone Attive, ";
            }
            if($request->nominativo){
              $nominativo = $request->nominativo;
              $q->where('nominativo', 'like', "$nominativo%");
              $msgSearch= $msgSearch."Nominativo=".$nominativo;
              $orderBy = "nominativo";
            }
            if($request->nome){
              $nome = $request->nome;
              $q->where('nome', 'like', "$nome%");
              $msgSearch= $msgSearch." Nome=".$nome;
              $orderBy = "nominativo";
            }
           
           if( $request->filled('cognome')){
              $cognome = $request->cognome;
              $q->where('cognome', 'like', "$cognome%");
              $msgSearch= $msgSearch." Cognome=".$cognome;
              $orderBy = "nome";
            }

            $criterio_nascita = $request->input('criterio_data_nascita',null);
            $nascita  = $request->input('data_nascita',null);

            if ($criterio_nascita and $nascita) {
              $q->where('data_nascita', $criterio_nascita, $nascita);
              $msgSearch= $msgSearch." Data Nascita".$criterio_nascita.$nascita;
            }
  
          });
      $persone = $queryLibri->orderBy($orderBy)->paginate(50);
      return view('nomadelfia.persone.search_results',["persone"=>$persone,"msgSearch"=> $msgSearch ]);
   }
  

  /**
   * Aggiorna lo stato di una persona.
   * 
   */
  public function modficaStatus(Request $request, $idPersona){
    $validatedData = $request->validate([
      "stato" => "required",
    ],[
      "stato.required" => "lo stato  è obbligatorie",
    ]);
    $persona = Persona::findOrFail($idPersona);
    $persona->stato = $request->stato;
    if($persona->save())
      return redirect()->route('nomadelfia.persone.dettaglio',['idPersona' =>$idPersona])->withSuccess("Stato di $persona->nominativo aggiornato correttamente. ");
    else    
    return redirect()->route('nomadelfia.persone.dettaglio',['idPersona' =>$idPersona])->withSError("Errore dureante l'aggiornamente dello stato dii $persona->nominativo.");
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
  
  // Inserimento nuova Persona
  public function insertInitialView(){
    return view("nomadelfia.persone.inserimento.initial");
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
    ],[
      "persona.required" => "Il cognome è obbligatorio",
    ]);
   
    if ($request->filled('persona')) {
      $personeEsistenti = Persona::where("nominativo","like","%".$request->persona."%")
                                  ->orWhere("nome", "like", "%".$request->persona."%")
                                  ->orWhere("cognome", "like", "%".$request->persona);
      if($personeEsistenti->exists() )
        return view("nomadelfia.persone.insert_existing", compact('personeEsistenti'));
      else
         return redirect(route('nomadelfia.persone.inserimento.anagrafici'))->withSuccess("Nessuna persona presente con nome e cognome inseriti.")->withInput();
    }
  }

  public function insertDatiAnagraficiView(){
    return view("nomadelfia.persone.inserimento.dati_anagrafici");
  }

  public function insertDatiAnagrafici(Request $request){
    $validatedData = $request->validate([
      "nominativo" => "required|unique:db_nomadelfia.persone,nominativo", 
      "nome" => "required",
      "cognome" => "required",
      "data_nascita" => "required|date",
      "luogo_nascita" => "required",
      "sesso" => "required",
    ],[
      "nominativo.required" => "Il nominativo è obbligatorio", 
      'nominativo.unique'=>"IL nominativo inserito esiste già.",
      "nome.required" => "Il nome è obbligatorie",
      "cognome.required" => "Il cognome è obbligatorio",
      "data_nascita.required" => "La data di nascita è obbligatoria",
      "luogo_nascita.required" => "IL luogo di nascita è obbligatorio",
      "sesso.required" => "Il sesso della persona è obbligatorio",
    ]);
    $persona = Persona::create(['nominativo'=>$request->input('nominativo'), 
                              'sesso'=>$request->input('sesso'),
                              'nome'=>$request->input('nome'),
                              "cognome"=>$request->input('cognome'),
                              "provincia_nascita"=>$request->input('luogo_nascita'),
                              'data_nascita'=>$request->input('data_nascita'),
                              // TODO: delete this column because the categoria is a many to many relationship in to the persone_categoria table
                              'categoria_id' =>$request->input('categoria_id'),
                              'id_arch_pietro'=>0,
                              'id_arch_enrico'=>0,]
                            );
    $res = $persona->save();
    if($res){
      return redirect(route('nomadelfia.persone.inserimento.entrata.scelta',['idPersona'=> $persona->id]))->withSuccess("Dati anagrafici di $persona->nominativo inseriti correttamente.");
    }else{
      return redirect(route('nomadelfia.persone.inserimento'))->withError("Errore. Persona $persona->nominativo non inserita.");
    }
  }

  public function insertPersonaInternaView(Request $request, $idPersona){
    $persona = Persona::findOrFail($idPersona);
    return view("nomadelfia.persone.inserimento.entrata", compact('persona'));
  }

  // Inserisci la persona come persona interna in Nomadelfia.
  public function insertPersonaInterna(Request $request, $idPersona){
    $validatedData = $request->validate([
      "tipologia" => "required",
      "data_entrata" => "required_unless:tipologia,dalla_nascita",
      "famiglia_id" => "required_unless:tipologia,maggiorenne_single,maggiorenne_famiglia",
      "gruppo_id" => "required_if:tipologia,maggiorenne_single,maggiorenne_famiglia",
    ],[
      "tipologia.required" => "La tipologia di entrata è obbligatoria",
      "data_entrata.required_unless" => "La data di entrata è obbligatoria",
      "famiglia_id.required_unless" => "La famiglia è obbligatoria",
      "gruppo_id.required_if" => "Il gruppo familiare  è obbligatoria",
      ]);
    
    $persona = Persona::findOrFail($idPersona);

    switch ($request->tipologia) {
      case "dalla_nascita":
          $persona->entrataNatoInNomadelfia($request->famiglia_id);
          break;
      case "minorenne_accolto":
          $persona->entrataMinorenneAccolto($request->data_entrata, $request->famiglia_id);
          break;
      case "minorenne_famiglia":
          $persona->entrataMinorenneConFamiglia($request->data_entrata, $request->famiglia_id);
          break;
      case "maggiorenne_single":
          $persona->entrataMaggiorenneSingle($request->data_entrata, $request->gruppo_id);
          break;
      case "maggiorenne_famiglia":
          $persona->entrataMaggiorenneSposato($request->data_entrata, $request->gruppo_id);
          break;
      default:
        return redirect()->back()->withErrore("Tipologia di entrata per $persona->nominativo non riconosciuta.");

    }
    return redirect()->route('nomadelfia.persone.dettaglio', [$persona->id])->withSuccess("Persona $persona->nominativo inserita correttamente.");
}

  public function insertFamiglia(Request $request, $idPersona){
    $validatedData = $request->validate([
      "famiglia_id" => "required",
      "posizione_famiglia"=> "required",
    ],[
      "famiglia_id.required" => "La famiglia è obbligatoria",
      "posizione_famiglia.required" => "La posizione nella famiglia è obbligatoria",
    ]);
    $persona = Persona::findOrFail($idPersona);

    $persona->famiglie()->attach($request->famiglia_id, ['stato' => '1', "posizione_famiglia"=>$request->posizione_famiglia]);
    return redirect()->route('nomadelfia.persone.dettaglio', [$persona->id])->withSuccess("Persona $persona->nominativo inserita correttamente.");
  }



  /**
   * Ritorna la view per la modifica delle famiglie di una persona
   * 
   * @author Davide Neri
   */
  public function famiglie($idPersona){
    $persona = Persona::findOrFail($idPersona);
    $attuale = $persona->famigliaAttuale();
    $storico = $persona->famiglieStorico;
    return view("nomadelfia.persone.famiglia.show", compact('persona', 'attuale', 'storico'));
  }


  /**
   * Ritorna la view per la modifica della posizione assegnata ad una persona
   * 
   * @author Davide Neri
   */
  public function posizione($idPersona){
    $persona = Persona::findOrFail($idPersona);
    $posattuale = $persona->posizioneAttuale; // ce ne possono essere più di una (per errori di isnerimento dati)
    $storico = $persona->posizioniStorico;
    return view("nomadelfia.persone.posizione.show", compact('persona', 'posattuale', "storico"));
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
    if($persona->posizioneAttuale->count() == 1) // se 
      $persona->posizioni()->updateExistingPivot($persona->posizioneAttuale()->first()->id, ['stato'=>'0','data_fine'=>($request->data_fine ? $request->data_fine: $request->data_inizio)]);
    $persona->posizioni()->attach($request->posizione_id, ['stato'=>'1','data_inizio'=>$request->data_inizio]);
    return redirect()->back()->withSuccess("Nuova posizione assegnata a $persona->nominativo  con successo.");
  }

  /**
   * Modifica la posizione  di una persona.
   * 
   * @author Davide Neri
   */
  public function modificaDataInizioPosizione(Request $request, $idPersona, $id){ 
    $validatedData = $request->validate([
      "current_data_inizio" => "required", 
      "new_data_inizio" => "required|date",
    ],[
      "new_data_inizio.date" => "La nuova data di inzio posizione non è una data valida", 
      'new_data_inizio.required'=>"La nuova data di inizio della posizione è obbligatoria.",
      'current_data_inizio.required'=>"La data di inizio della posizione è obbligatoria.",
    ]);
    $persona = Persona::findOrFail($idPersona);
    if ($persona->modificaDataInizioPosizione($id, $request->current_data_inizio, $request->new_data_inizio)){
      return redirect()->back()->withSuccess("Posizione modificata di $persona->nominativo  con successo");
    }
    return redirect()->back()->withError("IMpossibile aggiornare la posizione di  $persona->nominativo");
  }
  
   /**
   * Ritorna la view per la modifica della categoria di una persona
   * 
   * @author Davide Neri
   */
  public function categoria($idPersona){
    $persona = Persona::findOrFail($idPersona);
    $categoriaAttuale = $persona->categoriaAttuale();
    return view("nomadelfia.persone.categoria.show",compact('persona','categoriaAttuale'));
  }
  
  // assegnaCategoria
  public function assegnaCategoria(Request $request, $idPersona){
    $validatedData = $request->validate([
      "categoria_id" => "required", 
      "data_inizio" => "required|date",
      // "data_fine" => "required|date",
    ],[
      "categoria_id.required" => "La posizione è obbligatorio", 
      'data_inizio.required'=>"La data di inizio della posizione è obbligatoria.",
      // 'data_fine.required'=>"La data fine della posizione è obbligatoria.",
  ]);
    // dd($request->all());
    $persona = Persona::findOrFail($idPersona);
    // $persona->categoria_id = $request->categoria_id;
    if($persona->categoriaAttuale()) // se 
      $persona->categorie()->updateExistingPivot($persona->categoriaAttuale()->id, ['stato'=>'0','data_fine'=>($request->data_fine ? $request->data_fine: $request->data_inizio)]);
    $persona->categorie()->attach($request->categoria_id, ['stato'=>'1','data_inizio'=>$request->data_inizio]);
    return redirect()->back()->withSuccess("Nuova categoria assegnata a $persona->nominativo  con successo.");
  }

  /**
   * Elimina una categoria assegnata ad una persona
   * 
   * @author Davide Neri
   */
  public function eliminaCategoria(Request $request, $idPersona, $id){
    $persona =  Persona::findOrFail($idPersona);
    $res = $persona->categorie()->detach($id);
    if ($res){
      return redirect()->back()->withSuccess("Categoria rimossa consuccesso per $persona->nominativo ");
    }else{
      return redirect()->back()->withErro("Errore. Impossibile rimuovere la categoria per $persona->nominativo");
    }
  }

  

  /**
   * Modifica la posizione  di una persona.
   * 
   * @author Davide Neri
   */
  public function modificaCategoria(Request $request, $idPersona, $id){ 
    $validatedData = $request->validate([
      "data_fine" => "date", 
      "data_inizio" => "required|date",
      "stato" =>"required"
    ],[
      "data_fine.date" => "La data fine posizione dee essere una data valida", 
      'data_inizio.required'=>"La data di inizio della posizione è obbligatoria.",
      'stati.required'=>"Lo stato attuale è obbligatorio.",

  ]);
    $persona = Persona::findOrFail($idPersona);
    $persona->categorie()->updateExistingPivot($id, ['data_fine'=>$request->data_fine, 'data_inizio'=>$request->data_inizio, "stato"=>$request->stato]);
    return redirect()->back()->withSuccess("Categoria modificata di $persona->nominativo  con successo.");
  }

   /**
   * Elimina una posizione assegnata ad una persona
   * 
   * @author Davide Neri
   */
  public function eliminaPosizione(Request $request, $idPersona, $id){
    $persona =  Persona::findOrFail($idPersona);
    $res = $persona->posizioni()->detach($id);
    if ($res){
      return redirect()->back()->withSuccess("Posizione rimossa consuccesso per $persona->nominativo ");
    }else{
      return redirect()->back()->withErro("Errore. Impossibile rimuovere la posizione per $persona->nominativo");
    }
  }

    /**
   * Conclude una posizione assegnata ad una persona
   * 
   * @author Davide Neri
   */
  public function concludiPosizione(Request $request, $idPersona, $id){
    $validatedData = $request->validate([
      "data_inizio" => "required|date", 
      "data_fine" => "required|date|after_or_equal:data_inizio"
    ],[
      "data_inizio.date" => "La data di entrata non è  una data valida", 
      'data_inizio.required'=>"La data di entrata è obbligatoria",
      "data_fine.date" => "La data di uscita non è  una data valida", 
      "data_fine.required" => "La data di uscita  è obbligatoria", 
      "data_fine.after_or_equal" => "La data di fine posizione non può essere inferiore alla data di inizio", 
  ]);
    $persona = Persona::findOrFail($idPersona);
    $res = $persona->concludiPosizione($id, $request->data_inizio, $request->data_fine);
    if ($res){
      return redirect()->back()->withSuccess("Posizione di $persona->nominativo aggiornata con successo");
    }else{
      return redirect()->back()->withErro("Errore. Impossibile aggiornare la posizione di  $persona->nominativo");
    }
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
    return redirect()->back()->withSuccess("Stato assegnato a $persona->nominativo con successo");
  }

  public function modificaStato(Request $request, $idPersona, $id){
    $validatedData = $request->validate([
      "data_fine" => "date", 
      "data_inizio" => "required|date",
      "stato" =>"required"
    ],[
      "data_fine.date" => "La data fine posizione dee essere una data valida", 
      'data_inizio.required'=>"La data di inizio della posizione è obbligatoria.",
      'stato.required'=>"Lo stato attuale è obbligatorio.",
  ]);
    $persona = Persona::findOrFail($idPersona);
    $persona->stati()->updateExistingPivot($id, ['data_fine'=>$request->data_fine, 'data_inizio'=>$request->data_inizio, "stato"=>$request->stato]);
    return redirect()->back()->withSuccess("Stato di  $persona->nominativo  modificato con successo.");
  }

  /**
   * Ritorna la view per la modifica del gruppo familiare di una persona
   * 
   * @author Davide Neri
   */
  public function gruppoFamiliare($idPersona){
    $persona = Persona::findOrFail($idPersona);
    $attuale = $persona->gruppofamiliareAttuale();
    return view("nomadelfia.persone.gruppofamiliare.show", compact('persona', 'attuale'));
  }


  /**
   * Elimina la persona da un gruppo familiare
   * 
   * @author Davide Neri
   */
  public function eliminaGruppofamiliare(Request $request, $idPersona, $id){ 

    $persona = Persona::findOrFail($idPersona);
    $res = $persona->gruppifamiliari()->detach($id);
    if ($res){
      return redirect()->back()->withSuccess("$persona->nominativo rimosso/a dal gruppo familiare con successo");
    }else{
      return redirect()->back()->withErro("Errore. Impossibile rimuovere $persona->nominativo dal gruppo familiare.");
    }
  }


  /**
   * Conclude la persona in un gruppo familiare settando la data di uscita e lo stato = 0.
   * 
   * @author Davide Neri
   */
  public function concludiGruppofamiliare(Request $request, $idPersona, $id){ 
    $validatedData = $request->validate([
      "data_entrata" => "required|date", 
      "data_uscita" => "required|date|after_or_equal:data_entrata",
    ],[
      "data_entrata.date" => "La data di entrata non è una data valida", 
      'data_entrata.required'=>"La data di entrata è obbligatoria",
      "data_entrata.date" => "La data di uscita non è  una data valida", 
      "data_entrata.required" => "La data di uscota non è  una data valida", 
      "data_uscita.after_or_equal" => "La data di uscita non può essere inferiore alla data di entrata", 
  ]);

    $persona = Persona::findOrFail($idPersona);
    $res = $persona->concludiGruppoFamiliare($id, $request->data_entrata, $request->data_uscita);
    if ($res){
      return redirect()->back()->withSuccess("$persona->nominativo rimosso/a dal gruppo familiare con successo");
    }else{
      return redirect()->back()->withErro("Errore. Impossibile rimuovere $persona->nominativo dal gruppo familiare.");
    }
  }


  

   /**
   * Assegna un nuovo gruppo familiare ad una persona
   * 
   * @author Davide Neri
   */
  public function assegnaGruppofamiliare(Request $request, $idPersona){ 
    $validatedData = $request->validate([
      "gruppo_id" => "required", 
      "data_entrata" => "required|date",
    ],[
      "gruppo_id.required" => "Il nuovo gruppo è obbligatorio", 
      'data_entrata.required'=>"La data di entrata nel gruppo familiare è obbligatoria.",
  ]);
    $persona = Persona::findOrFail($idPersona);
    $attuale = $persona->gruppofamiliareAttuale();
    if (count($attuale) > 0){
      return redirect()->back()->withError("Errore. $persona->nominativo ha gia un gruppo familiare attivo.");
    }

    $res = $persona->gruppifamiliari()->attach($request->gruppo_id, ['stato'=>'1','data_entrata_gruppo'=>$request->data_entrata]);
    if ($res){ // se ha già uno stato attuale aggiorna lo stato attuale
      return redirect()->back()->withSuccess("$persona->nominativo assegnato al gruppo familiare con successo");
    }else{
      return redirect()->back()->withErro("Errore. La persona ha più di un gruppo attivo. LAsciara ad assegnare $persona->nominativo  al gruppo familiare.");
    }
  }

   /**
   * Sposta una persona da un gruppo familiare ad un  nuovo gruppo familiare settando al data di entrata e uscita
   * 
   * @author Davide Neri
   */
  public function spostaNuovoGruppofamiliare(Request $request, $idPersona, $id){ 
    $validatedData = $request->validate([
      "new_gruppo_id" => "required", 
      "new_data_entrata" => "required|date", // data entrata delnuovo gruppo familiare
      "current_data_entrata" => "required|date", // data entrata del vecchio gruppo familiare
    ],[
      "new_gruppo_id.required" => "Il nuovo gruppo è obbligatorio", 
      'new_data_entrata.required'=>"La data di entrata nel nuovo gruppo familiare è obbligatoria.",
      'current_data_entrata.required'=>"La data di entrata nel gruppo familiare è obbligatoria.",
  ]);
   // se la data  di uscita del nuovo gruppo non è stata indicata, viene settata uguale all data di entrata nel nuovo gruppo
    $new_datain = $request->new_data_entrata;
    $current_data_uscita = $request->input('current_data_uscita', $new_datain);
    $persona = Persona::findOrFail($idPersona);

    $persona->spostaPersonaInGruppoFamiliare($id, $request->current_data_entrata, $current_data_uscita, $request->new_gruppo_id, $new_datain);
    return redirect()->back()->withSuccess("$persona->nominativo assegnato al gruppo familiare con successo");
  }

  public function modificaGruppofamiliare(Request $request, $idPersona, $id){
    $validatedData = $request->validate([
      "current_data_entrata" => "required|date",
      "new_data_entrata"=> "required|date",
    ],[
      "current_data_entrata.date" => "La data corrente di entrata non è una data valida", 
      'current_data_entrata.required'=>"La data corrente di entrata dal gruppo è obbligatoria.",
      'new_data_entrata.required'=>"La data corrente di entrata dal gruppo è obbligatoria.",
      'new_data_entrata.date'=>"La data corrente di entrata non è una data valida",
    ]);
    $persona = Persona::findOrFail($idPersona);
    
    if ($persona->updateDataInizioGruppoFamiliare($id,  $request->current_data_entrata, $request->new_data_entrata)){
      return redirect()->back()->withSuccess("Gruppo familiare $persona->nominativo  modificato con successo.");
    }
    return redirect()->back()->withError("Impossibile aggiornare la data di nizio del gruppo familiare.");
  }
  
  public function aziende(Request $request, $idPersona){
    $persona = Persona::findOrFail($idPersona);
    return view("nomadelfia.persone.aziende.show",compact('persona'));
  } 


  /**
   * Assegna una nuova azienda alla persona
   * 
   * @author Davide Neri
   */
  public function  assegnaAzienda(Request $request, $idPersona){ 
    $validatedData = $request->validate([
      "azienda_id" => "required", 
      "mansione" => "required", 
      "data_inizio" => "required|date",
    ],[
      "azienda_id.required" => "L'azienda è obbligatoria", 
      'data_inizio.required'=>"La data di inizio dell'azienda è obbligatoria.",
      'mansione.required'=>"La mansione del lavoratore nell'azienda è obbligatoria.",

  ]);
    $persona = Persona::findOrFail($idPersona);
    $azienda = Azienda::findOrFail($request->azienda_id);
    if($persona->aziendeAttuali->contains($azienda->id)) // la persona è stata già asseganta all'azienda
      return redirect()->back()->withError("$persona->nominativo è già assegnata all'azienda $azienda->nome_azienda");
      
    $persona->aziende()->attach($azienda->id, ['stato'=>'Attivo','data_inizio_azienda'=>$request->data_inizio,'mansione'=>$request->mansione]);
    return redirect()->back()->withSuccess("$persona->nominativo assegnato all'azienda $azienda->nome_azienda come $request->mansione con successo");
  }

  public function modificaAzienda(Request $request, $idPersona, $id){
    $validatedData = $request->validate([
      "mansione" => "required", 
      "data_entrata" => "required|date",
      "stato" => "required", 
    ],[
      'data_entrata.required'=>"La data di inizio dell'azienda è obbligatoria.",
      'mansione.required'=>"La mansione del lavoratore nell'azienda è obbligatoria.",
      'stato.required'=>"Lo stato è obbligatoria.",
    ]);
    $persona = Persona::findOrFail($idPersona);
    $azienda = Azienda::findOrFail($id);
    $persona->aziende()->updateExistingPivot($id, ['stato'=>$request->stato,'data_inizio_azienda'=>$request->data_entrata,'data_fine_azienda'=>$request->data_uscita, 'mansione'=>$request->mansione]);
    return redirect()->back()->withSuccess("Azienda $azienda->nome_azienda di $persona->nominativo  modificata con successo.");
  }

  public function createAndAssignFamiglia(Request $request, $idPersona){
    $validatedData = $request->validate([
      "nome" => "required|unique:db_nomadelfia.famiglie,nome_famiglia", 
      "posizione_famiglia" => "required",
      "data_creazione" => "required|date",
    ],[
      'nome.required'=>"Il nome della famiglia è obbligatorio",
      'nome.unique'=>"Il nome della famiglia esiste già",
      'posizione_famiglia.required'=>"La posizione è obbligatoria",
      'data_creazione.required'=>"Lo data di creazione della famiglia è obbligatoria",
    ]);
    $persona = Persona::findOrFail($idPersona);
    $attuale = $persona->famigliaAttuale();
    if ($attuale){
        return redirect()->back()->withError("La persona $persona->nomativo è già assegnata alla famiglia $attuale->nome.");
    }
    $res = $persona->createAndAssignFamiglia($idPersona,$request->posizione_famiglia, $request->nome, $request->data_creazione, $request->data_entrata);
    if ($res){
      return redirect()->back()->withSuccess("Persona $persona->nominativo e famiglia $request->nome creata con successo");
    }else{
      return redirect()->back()->withError("Impossibile creare la famiglia $request->nome");
    }
  }

  public function spostaInNuovaFamiglia(Request $request, $idPersona){
    $validatedData = $request->validate([
      "new_famiglia_id" => "required", 
      "new_posizione_famiglia" => "required",
      "new_data_entrata" => "required",
      "old_data_uscita" => "date",
    ],[
      'new_famiglia_id.required'=>"Il nome della famiglia è obbligatorio",
      'new_posizione_famiglia.required'=>"La posizione è obbligatoria",    
      'new_data_entrata.required'=>"La data di entrata nella nuova famiglia è obbligatoria",
      'old_data_entrata.date'=>"Lo data di entrata dalla famiglia non è valida",
      'old_data_uscita.date'=>"Lo data di uscita dalla famiglia non è valida",
    ]);
    $persona = Persona::findOrFail($idPersona);
    $famiglia = Famiglia::findOrFail($request->new_famiglia_id);
    $res = $persona->spostaNellaFamiglia($famiglia,  $request->new_data_entrata, $request->new_posizione_famiglia, $request->old_data_uscita);
    if ($res){
      return redirect()->back()->withSuccess("Persona $persona->nominativo spostata nella famiglia con successo");
    }else{
      return redirect()->back()->withError("Impossibile spostare la persona $persona->nominativo nella famiglia");
    }
  }

  

}
