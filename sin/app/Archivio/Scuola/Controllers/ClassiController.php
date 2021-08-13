<?php
namespace App\Nomadelfia\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;

use Illuminate\Http\Request;

use App\Nomadelfia\Models\AnnoScolastico;

class ClassiController extends CoreBaseController
{
  /**
  * view della pagina di gestione delle aziende
  * @author Matteo Neri
  **/
  public function view(){
    $aziende =  AnnoScolastico::aziende()->orderBy("nome_azienda")->with('lavoratoriAttuali')->get();
    return view('nomadelfia.aziende.index',compact('aziende'));
  }

  /**
  * ritorna la view per editare una azienda
  * @param id dell'azienda da editare
  * @author Matteo Neri
  **/
  public function edit($id){
    $azienda = AnnoScolastico::findOrFail($id);
    return view('nomadelfia.aziende.edit', compact('azienda'));

  }


}
