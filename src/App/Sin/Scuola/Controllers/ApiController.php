<?php

namespace App\Scuola\Controllers;

use App\Core\Controllers\BaseController;
use App\Scuola\Models\Classe;
use Carbon\Carbon;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;

class ApiController extends BaseController
{
    public function alunni(Request $request)
    {
        $term = $request->term;
        $persone = Persona::where('nominativo', 'LIKE', "$term%")->DaEta(3, 18)->get();
        $results = [];
        foreach ($persone as $persona) {
            $results[] = ['value' => $persona->id, 'label' => $persona->nominativo];
        }

        return response()->json($results);
    }

    public function alunniPossibili(Request $request, $id)
    {
        $classe = Classe::findOrFail($id);
        $persone = $classe->alunniPossibili();
        $results = [];
        foreach ($persone as $persona) {
            $year = Carbon::createFromFormat('Y-m-d', $persona->data_nascita)->year;
            $results[] = ['value' => $persona->id, 'label' => "($year) $persona->nominativo ($persona->nome  $persona->cognome)"];
        }

        return response()->json($results);
    }
}
