<?php
namespace App\Nomadelfia\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;

use Illuminate\Support\Facades\Input;
use App\Nomadelfia\Models\Persona;

class AutocompleteController extends CoreBaseController
{

  public function autocompletePersona()
  {
    $term = Input::get('term');
    $persone = Persona::where("nominativo", "LIKE", "$term%")->orderBy("nominativo")->get();
    $results = array();
    foreach ($persone as $persona)
        $results[] = ['value'=>$persona->id,'label'=>$persona->nominativo];
    return response()->json($results);
  }

}
