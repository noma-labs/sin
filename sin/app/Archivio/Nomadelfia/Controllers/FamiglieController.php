<?php
namespace App\Nomadelfia\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;

use Illuminate\Http\Request;

use App\Nomadelfia\Models\Famiglia;

class FamiglieController extends CoreBaseController
{
 
  /**
  * Ritorna la view di gestione delle famiglie
  * @author Davide Neri
  **/
  public function view(){
    $capifamiglie = Famiglia::onlyCapoFamiglia();
    $single = Famiglia::onlySingle();
    return view('nomadelfia.famiglie.index',compact('capifamiglie','single'));
  }

  /**
  * Ritorna la view di gestione din una singola famiglia
  * @author Davide Neri
  **/
  public function show(Request $request, $id){ 
    $famiglia = Famiglia::findorfail($id);
    return view('nomadelfia.famiglie.show',compact('famiglia'));
  }

}
