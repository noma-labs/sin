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
    $capifamiglieMaschio = Famiglia::onlyCapoFamiglia()->maschio();
    $capifamiglieFemmina = Famiglia::onlyCapoFamiglia()->femmina();

    $singleMaschio = Famiglia::onlySingle()->maschio();
    $singleFemmine = Famiglia::onlySingle()->femmina();

    return view('nomadelfia.famiglie.index',compact('capifamiglieMaschio','capifamiglieFemmina','singleMaschio','singleFemmine'));
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
