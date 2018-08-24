<?php
namespace App\Nomadelfia\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;

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


  public function insert(Request $request){
    $rules = [
      "nome" => "required",
      "cognome" => "required",
      "sesso" => "required",
      "nascita" => "required|date",
      "citta" => "required",
      "nominativo" => "required|unique:db_nomadelfia.persone,nominativo", //'unique:connection.users,email_address'
      "posizione" => "required",
      "inizio" => "required|date",
      "famiglia" => "required",
      "nucleo" => "required",
      "gruppo" => "required",
      "data_gruppo" => "required|date"
    ];

    $validator = Validator::make($request->all(), $rules);

    // validazione condizionale di "azienda" e "incarico"
    $validator->sometimes('data_lavoro', 'required', function($input){
      return $input->azienda != '';
    });
    $validator->sometimes('data_incarico', 'required', function($input){
      return $input->incarico != '';
    });

    if($validator->fails()){
      return back()
                ->withInput()
                ->withErrors($validator);
    }
    // SALVATAGGIO INFO NEL DB

    // salvataggio persona
    $persona = new Persona;
    $persona->nominativo = $request->input('nominativo');
    $persona->data_nascita_persona = $request->input('nascita');
    $persona->id_arch_pietro = 0;
    $persona->id_arch_enrico = 0;
    $persona->save();

    $persona->posizioni()->attach($request->input('posizione'), ['data_inizio' => $request->input('inizio')]);
    $persona->famiglie()->attach($request->input('famiglia'), ['nucleo_famigliare_id' => $request->input('nucleo')]);
    $persona->gruppi()->attach($request->input('gruppo'), ['data_entrata_gruppo' => $request->input('data_gruppo')]);
    if ($request->input('azienda') != ''){
      $persona->aziende()->attach($request->input('azienda'), ['data_inizio_azienda' => $request->input('data_lavoro')]);
    }

    if ($request->input('incarico') != ''){
      $persona->incarichi()->attach($request->input('incarico'), ['data_inizio' => $request->input('data_incarico')]);
    }

    // salvataggio dati personali
    $dati_personali = new DatiPersonali;
    $dati_personali->persona_id = $persona->id;
    $dati_personali->nome = $request->input('nome');
    $dati_personali->cognome = $request->input('cognome');
    $dati_personali->sesso = $request->input('sesso');
    $dati_personali->data_nascita = $request->input('nascita');
    $dati_personali->provincia_nascita = $request->input('citta');
    $dati_personali->save();

    return redirect(route('persone.inserimento_form'))->withSuccess('Iserimento completato');
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
