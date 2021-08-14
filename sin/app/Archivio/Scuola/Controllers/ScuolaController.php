<?php
namespace App\Scuola\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use App\Nomadelfia\Models\AnnoScolastico;
use App\Scuola\Models\Anno;

class ScuolaController extends CoreBaseController
{

  public function index(){
    $classi = Anno::getLastAnno();
    return view('scuola.summary',compact('classi'));
  }

}
