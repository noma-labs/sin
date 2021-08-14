<?php
namespace App\Scuola\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use App\Nomadelfia\Models\AnnoScolastico;
use App\Scuola\Models\Anno;

class ScuolaController extends CoreBaseController
{

  public function index(){
    $anno = Anno::getLastAnno();
    $alunni = $anno->alunni();
    return view('scuola.summary',compact('anno', 'alunni'));
  }

}
