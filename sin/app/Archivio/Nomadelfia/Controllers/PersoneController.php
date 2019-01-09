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
      return redirect()->route('nomadelifa.persone.dettaglio',['idPersona' =>$idPersona])->withSuccess("Dati anagrafici di $persona->nominativo aggiornati correttamente. ");
    else    
    return redirect()->route('nomadelifa.persone.dettaglio',['idPersona' =>$idPersona])->withSError("Errore dureante l'aggiornamente dei dati anagrafici di $persona->nominativo.");


  }

  public function modificaNominativo($idPersona){
    $persona = Persona::findOrFail($idPersona);
    return view('nomadelfia.persone.edit_nominativo',compact('persona'));
  }

  public function modificaNominativoConfirm(Request $request,$idPersona){
    $validatedData = $request->validate([
      "nominativo" => "required",
      // "nuovonominativo" => "required",
    ],[
      "nominativo.required" => "Il nominativo è obbligatorio",
      "nuovonominativo.required" => "Il nuovo nominativo  è obbligatorio",
    ]);
    $persona = Persona::findOrFail($idPersona);

    if($request->operazione == "modifica"){
      $persona->nominativo = $request->nominativo;
      $persona->save();
      return redirect()->route('nomadelifa.persone.dettaglio', ['idPersona' => $idPersona])->withSucces("Nominativo aggiornato con suceesso");
    }else{
      $persona->nominativiStorici()->create(['nominativo'=> $persona->nominativo]);
      $persona->nominativo = $request->nuovonominativo;
      $persona->save();
      return redirect()->route('nomadelifa.persone.dettaglio', ['idPersona' => $idPersona])->withSucces("Nuovo nominativo aggiunto con successo.");
    }

  }

  public function modificaCategoriaConfirm(Request $request, $idPersona){
    $validatedData = $request->validate(["categoria" => "required",],
                                        ["categoria.required" => "La categoria è obbligatoria",
    ]);
    $persona = Persona::findOrFail($idPersona);
    $persona->categoria_id = $request->categoria;
    if($persona->save())
      return redirect()->route('nomadelifa.persone.dettaglio', ['idPersona' => $idPersona])->withSucces("Stato attuale modificato con successo.");
    else
      return redirect()->route('nomadelifa.persone.dettaglio', ['idPersona' => $idPersona])->withError("Errore: Stato attuale non aggiornato correttamente.");

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
   * Inserisci una persona nel sistema con i suoi dati personali.
   * L'inserimento assegna l'ID ad una persona che la identifica in maniera univoca.
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
      ],[
        "nominativo.required" => "Il nominativo è obbligatorio", 
        'nominativo.unique'=>"IL nominativo inserito esiste già.",
        "nome.required" => "Il nome è obbligatorie",
        "cognome.required" => "Il cognome è obbligatorio",
        "data_nascita.required" => "La data di nascita è obbligatoria",
        "luogo_nascita.required" => "IL luogo di nascita è obbligatorio",
        "sesso.required" => "Il sesso della persona è obbligatorio",
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
                                'id_arch_pietro'=>0,
                                'id_arch_enrico'=>0,]
                              );
      $persona->save();
      if($_addanother)
        return redirect(route('nomadelfia.persone.inserimento'))->withSuccess("Persona $persona->nominativo inserita correttamente.");
      if($_addonly)
        return redirect()->route('nomadelifa.persone.dettaglio', [$persona->id])->withSuccess("Persona $persona->nominativo inserita correttamente.");
      
    }
    catch (Illuminate\Database\QueryException $e){
        $error_code = $e->errorInfo[1];
        if($error_code == 1062){
            return redirect(route('nomadelfia.persone.inserimento'))->withError('Persona già esistente con il nominativo.');
        }
        return redirect(route('nomadelfia.persone.inserimento'))->withError("Errore generale nell'esecusion della query");
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

  public function insertConfirm(Request $request){ //InsertClientiRequest $request

  }

  // public function searchPersona(Request $request){
  //   $term = $request->term;
  //   if($term)
  //      $persone = Persona::where("nominativo", "LIKE", "$term%")->orderBy("nominativo")->get();
  //
  //   if($persone->count() > 0){
  //     foreach ($persone as $persona)
  //     {
  //         $results[] = ['value'=>$persona->id, 'label'=>$persona->nominativo];
  //     }
  //     return response()->json($results);
  //   }else {
  //     return response()->json(['value'=>"", 'label'=> "persona non esiste"]);
  //   }
  //
  // }

}
