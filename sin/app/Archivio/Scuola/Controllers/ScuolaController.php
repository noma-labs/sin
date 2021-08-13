<?php
namespace App\Scuola\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;

use App\Nomadelfia\Models\Classe;
use Illuminate\Http\Request;

use App\Nomadelfia\Models\AnnoScolastico;

class ScuolaController extends CoreBaseController
{

  public function index(){
    $classi =  Classe::perAnno("2021/2022");
    return view('scuola.summary',compact('classi'));
  }

}
