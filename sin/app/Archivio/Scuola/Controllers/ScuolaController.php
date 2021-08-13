<?php
namespace App\Scuola\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;

use App\Scuola\Models\Anno;
use App\Scuola\Models\Classe;
use Illuminate\Http\Request;

use App\Nomadelfia\Models\AnnoScolastico;

class ScuolaController extends CoreBaseController
{

  public function index(){
    $classi =  Anno::getLastAnno();
    return view('scuola.summary',compact('classi'));
  }

}
