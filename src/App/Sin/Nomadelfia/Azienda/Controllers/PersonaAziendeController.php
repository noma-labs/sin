<?php

namespace App\Nomadelfia\Azienda\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;

class PersonaAziendeController extends CoreBaseController
{
    public function index($idPersona)
    {
        $persona = Persona::findOrFail($idPersona);
        return view('nomadelfia.persone.aziende.show', compact('persona'));
    }

}
