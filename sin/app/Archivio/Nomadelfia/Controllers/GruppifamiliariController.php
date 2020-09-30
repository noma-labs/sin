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


    // TODO: mettere il controllo se una persona è senza famiglia nella pagina delle famiglie.
    // il controllo nella query (famiglie_persone.stato IS NULL) viene usato per selezaionre anche le persone senza una famiglia.
    $single =  DB::connection('db_nomadelfia')->select(
      DB::raw("SELECT famiglie_persone.famiglia_id, famiglie.nome_famiglia, persone.id as persona_id, persone.nominativo, famiglie_persone.posizione_famiglia, persone.data_nascita 
              FROM gruppi_persone 
              LEFT JOIN famiglie_persone ON famiglie_persone.persona_id = gruppi_persone.persona_id 
              INNER JOIN persone ON gruppi_persone.persona_id = persone.id 
              LEFT JOIN famiglie ON famiglie_persone.famiglia_id = famiglie.id 
              WHERE gruppi_persone.gruppo_famigliare_id = :gruppo
                  AND gruppi_persone.stato = '1' 
                  AND (famiglie_persone.stato = '1' OR famiglie_persone.stato IS NULL) 
                  AND (famiglie_persone.posizione_famiglia = 'SINGLE' OR famiglie_persone.stato IS NULL)
              ORDER BY persone.sesso, persone.data_nascita  ASC"), 
              array('gruppo' => $id)
    );


    $famiglie = DB::connection('db_nomadelfia')->select( 
                DB::raw("SELECT famiglie_persone.famiglia_id, famiglie.nome_famiglia, persone.id as persona_id, persone.nominativo, famiglie_persone.posizione_famiglia, persone.data_nascita 
                FROM gruppi_persone 
                LEFT JOIN famiglie_persone ON famiglie_persone.persona_id = gruppi_persone.persona_id 
                INNER JOIN persone ON gruppi_persone.persona_id = persone.id 
                LEFT JOIN famiglie ON famiglie_persone.famiglia_id = famiglie.id 
                WHERE gruppi_persone.gruppo_famigliare_id = :gruppo 
                    AND gruppi_persone.stato = '1' 
                    AND (famiglie_persone.stato = '1' OR famiglie_persone.stato IS NULL)
                    AND (famiglie_persone.posizione_famiglia != 'SINGLE' OR famiglie_persone.stato IS NULL)
                ORDER BY  persone.data_nascita ASC"), array('gruppo' => $id));
   
    $famiglie = collect($famiglie)->groupBy('famiglia_id');

    return view("nomadelfia.gruppifamiliari.edit",compact('gruppo','countPosizioniFamiglia', "single", "famiglie"));

  }


}
