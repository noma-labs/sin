<?php
namespace App\Patente\Controllers;
use App\Patente\Models\Patente as Patente;
use App\Nomadelfia\Models\Persona;
use Illuminate\Http\Request;
use App\Core\Controllers\BaseController as CoreBaseController;


class PatenteController extends CoreBaseController
{
    public function patente(){
       // $viewData = Persona::with("patenti.categorie")->orderBy("nominativo")->get();
        $viewData = Patente::with(['persone', 'categorie'])->orderBy("persona_id")->paginate(10);
        //dd($viewData);
        return view("patente.index")->with('viewdata', $viewData);
      }
}