<?php
namespace App\Nomadelfia\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use Illuminate\Http\Request;
use App\Nomadelfia\Models\GruppoFamiliare;

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
    return view("nomadelfia.gruppifamiliari.edit",compact('gruppo'));


  }

  public function  editConfirm(Request $request, $idPersona){
 }

  public function insert(){
  }

  public function insertConfirm(Request $request){ //InsertClientiRequest $request

  }
  

}
