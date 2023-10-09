<?php

namespace App\Nomadelfia\Azienda\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use Domain\Nomadelfia\Azienda\Models\Azienda;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;

class AziendeController extends CoreBaseController
{
    /**
     * view della pagina di gestione delle aziende
     *
     * @author Matteo Neri
     **/
    public function view()
    {
        $aziende = Azienda::aziende()->orderBy('nome_azienda')->with('lavoratoriAttuali')->get();

        return view('nomadelfia.aziende.index', compact('aziende'));
    }

    /**
     * ritorna la view per editare una azienda
     *
     * @param id dell'azienda da editare
     *
     * @author Matteo Neri
     **/
    public function edit($id)
    {
        $azienda = Azienda::findOrFail($id);

        return view('nomadelfia.aziende.edit', compact('azienda'));

    }

    public function editConfirm(Request $request, $idPersona)
    {
    }

    public function insert()
    {
    }

    public function insertConfirm(Request $request) //InsertClientiRequest $request
    {
    }

    public function searchPersona(Request $request)
    {
        $term = $request->term;
        if ($term) {
            $query = Persona::where('nominativo', 'LIKE', "$term%")->orderBy('nominativo');
        } else {
            $query = Persona::orderBy('nominativo');
        }

        $persone = $query->get();

        if ($persone->count() > 0) {
            $results = [];
            foreach ($persone as $persona) {
                $results[] = ['value' => $persona->id, 'label' => $persona->nominativo];
            }

            return response()->json($results);
        } else {
            return response()->json(['value' => '', 'label' => 'persona non esiste']);
        }

    }
}
