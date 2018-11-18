<?php
namespace App\Nomadelfia\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;

use Illuminate\Database\QueryException;

use Illuminate\Http\Request;

use App\Nomadelfia\Models\Persona;
use App\Nomadelfia\Models\Posizione;
use App\Nomadelfia\Models\Famiglia;
use App\Anagrafe\Models\Provincia;
use App\Anagrafe\Models\DatiPersonali;
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

  public function edit($idPersona){
    $persona = Persona::findOrFail($idPersona);

  }

  public function  editConfirm(Request $request, $idPersona){
 }

  public function insertView(){

    $posizioni = Posizione::orderBy('nome', 'asc')->get();
    $famiglie = Famiglia::orderBy('nome_famiglia', 'asc')->get();
    $provincie = Provincia::orderBy('sigla', 'asc')->get();
    $gruppi = GruppoFamiliare::orderBy('nome', 'asc')->get();
    $aziende = Azienda::orderBy('nome_azienda', 'asc')->get();
    $incarichi = Incarico::orderBy('nome', 'asc')->get();
    $nuclei_famigliari = [];
    return view("nomadelfia.persone.insert", compact('posizioni', 'famiglie', 'provincie', 'gruppi', 'aziende', 'incarichi', 'nuclei_famigliari'));
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
    // // validazione condizionale di "azienda" e "incarico"
    // $validator->sometimes('data_lavoro', 'required', function($input){
    //   return $input->azienda != '';
    // });
    // $validator->sometimes('data_incarico', 'required', function($input){
    //   return $input->incarico != '';
    // });

    try{
     // Salvo la persona nel DB_nomadelfia.persone
      $persona = Persona::create(['nominativo'=>$request->input('nominativo'), 
                                'data_nascita_persona'=>$request->input('data_nascita'),
                                'sesso'=>$request->input('sesso'),
                                'id_arch_pietro'=>0,
                                'id_arch_enrico'=>0,]
                              );

      // salvataggio dati personali nel DB_Anagrafe.dati_personali
      $persona->datiPersonali()->save( 
            new DatiPersonali(['nome'=>$request->input('nome'),
                              "cognome"=>$request->input('cognome'),
                              'data_nascita'=>$request->input('data_nascita'),
                              "provincia_nascita"=>$request->input('luogo_nascita'),
                              "sesso"=>$request->input('sesso')
                              ]));
      return redirect(route('nomadelfia.persone.inserimento'))->withSuccess("Persona $persona->nominativo inserita correttamente.");
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
