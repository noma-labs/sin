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
    $g = GruppoFamiliare::countComponenti();
    $gruppifamiliari = GruppoFamiliare::orderby('nome')->get();
    return view('nomadelfia.gruppifamiliari.index',compact('gruppifamiliari', 'g'));
  }

  public function show($idPersona){
    $persona = Persona::findOrFail($idPersona);
    return view("nomadelfia.gruppifamiliari.show",compact('persona'));
  }

  public function edit(Request $request,$id){
    $gruppo = GruppoFamiliare::findOrFail($id);
    $single = $gruppo::Single();
    $famiglie = $gruppo::Famiglie();

    return view("nomadelfia.gruppifamiliari.edit",compact('gruppo', "single", "famiglie"));

  }


}
