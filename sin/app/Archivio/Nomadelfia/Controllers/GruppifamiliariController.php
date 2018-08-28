<?php
namespace App\Nomadelfia\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use Illuminate\Http\Request;
use App\Nomadelfia\Models\GruppoFamiliare;

class GruppifamiliariController extends CoreBaseController
{
  public function view(){
  
    $gruppifamiliari = GruppoFamiliare::with("famiglie.componenti")->get();

    // GruppoFamiliare::with('famiglie.componenti')
    //    ->join('exam', 'students.id', '=', 'exam.student_id')
    //    ->orderBy('exam.result', 'DESC')
    //    ->get();
    // $gruppifamiliari = GruppoFamiliare::with(["famiglie", function($famiglia)
    // {
    //   $famiglia->with(['componenti',function($componente){
    //     $componente->orderby('data_nascita_persona');
    //   }]);
    // }]);
    // dd($gruppifamiliari);

    // $g = GruppoFamiliare::select('nome')
    //                     ->join('gruppi_famiglie',"gruppi_familiari.id","=", '')
    return view('nomadelfia.gruppifamiliari.index',compact('gruppifamiliari','gruppi_with_nucleifamiliari'));
  }

  public function show($idPersona){
    $persona = Persona::findOrFail($idPersona);
    return view("nomadelfia.gruppifamiliari.show",compact('persona'));
  }

  public function edit($idPersona){
    $persona = Persona::findOrFail($idPersona);

  }

  public function  editConfirm(Request $request, $idPersona){
 }

  public function insert(){
  }

  public function insertConfirm(Request $request){ //InsertClientiRequest $request

  }
  

}
