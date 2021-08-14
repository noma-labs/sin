<?php

namespace App\Scuola\Controllers;

use App\Core\Controllers\BaseController;
use App\Nomadelfia\Models\NucleoFamigliare;
use App\Nomadelfia\Models\Persona;
use Illuminate\Http\Request;


class ApiController extends BaseController
{
    function alunni(Request $request)
    {
        $term = $request->term;
        $persone = Persona::where("nominativo", "LIKE", "$term%")->DaEta(3,18)->get();
        $results = array();
        foreach ($persone as $persona) {
            $results[] = ['value' => $persona->id, 'label' => $persona->nominativo];
        }
        return response()->json($results);
    }

}
