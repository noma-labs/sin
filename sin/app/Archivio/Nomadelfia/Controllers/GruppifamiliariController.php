<?php
namespace App\Nomadelfia\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use Illuminate\Http\Request;
use App\Nomadelfia\Models\GruppoFamiliare;
use Illuminate\Support\Facades\DB;
use App;


class GruppifamiliariController extends CoreBaseController
{
  public function view(){
  
    $gruppifamiliari = GruppoFamiliare::with("famiglie.componenti")->orderby('nome')->get();
    return view('nomadelfia.gruppifamiliari.index',compact('gruppifamiliari','gruppi_with_nucleifamiliari'));
  }

  public function show($idPersona){
    $persona = Persona::findOrFail($idPersona);
    return view("nomadelfia.gruppifamiliari.show",compact('persona'));
  }

  public function edit(Request $request,$id){
    $gruppo = GruppoFamiliare::findOrFail($id);

    $countPosizioniFamiglia = GruppoFamiliare::CountPosizioniFamiglia($id)->get();

    $single =  DB::connection('db_nomadelfia')->select(
      DB::raw("SELECT famiglie_persone.famiglia_id, famiglie.nome_famiglia
      FROM gruppi_persone
        INNER join famiglie_persone ON famiglie_persone.persona_id = gruppi_persone.persona_id
        INNER join famiglie ON famiglie.id = famiglie_persone.famiglia_id
      where famiglie_persone.posizione_famiglia = 'SINGLE' and gruppi_persone.gruppo_famigliare_id = :gruppo"), array('gruppo' => $id));

    // ritorna tutte le famiglie "CApo FAMIGLIA" in un gruppo familiare
    $cp =  DB::connection('db_nomadelfia')->select( 
      DB::raw("SELECT famiglie.*
      FROM gruppi_persone
        INNER join famiglie_persone ON famiglie_persone.persona_id = gruppi_persone.persona_id
        INNER join famiglie ON famiglie.id = famiglie_persone.famiglia_id
      where famiglie_persone.posizione_famiglia = 'CAPO FAMIGLIA' and gruppi_persone.stato = '1' and gruppi_persone.gruppo_famigliare_id = :gruppo"), array('gruppo' => $id));

   $capoFamiglie = collect();
  foreach ($cp as $famiglia){
    $p = DB::connection('db_nomadelfia')->select( 
      DB::raw("SELECT persone.id, famiglie_persone.posizione_famiglia, persone.data_nascita, persone.nominativo,famiglie.*
        FROM persone
          INNER join gruppi_persone ON gruppi_persone.persona_id = persone.id
          INNER JOIN famiglie_persone ON famiglie_persone.persona_id = persone.id
          INNER JOIN famiglie ON famiglie.id = famiglie_persone.famiglia_id
        where famiglie_persone.famiglia_id = :famiglia  and famiglie_persone.stato = '1' and gruppi_persone.stato = '1'
        order by persone.data_nascita ASC "), array('famiglia' => $famiglia->id)); 
      $capoFamiglie[$famiglia->nome_famiglia] = $p;
    }
    return view("nomadelfia.gruppifamiliari.edit",compact('gruppo','countPosizioniFamiglia', "capoFamiglie", "single"));

  }


}
