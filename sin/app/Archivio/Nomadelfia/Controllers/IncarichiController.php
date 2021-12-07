<?php

namespace App\Nomadelfia\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;

use Illuminate\Http\Request;

use App\Nomadelfia\Models\Azienda;

class IncarichiController extends CoreBaseController
{
    /**
     * view della pagina di gestione delle aziende
     * @author Matteo Neri
     **/
    public function view()
    {
        $incarichi = Azienda::incarichi()->with('lavoratoriAttuali')->get();
        return view('nomadelfia.incarichi.index', compact('incarichi'));
    }

//    public function delete($idIncarico)
//    {
//        $incarico = Azienda::incarichi()->findOrFail($idIncarico);
//        $lav = $incarico->lavoratoriAttuali()->get();
//
//    }

    /**
     * ritorna la view per editare una azienda
     * @param id dell'azienda da editare
     * @author Matteo Neri
     **/
    public function edit($id)
    {
        $incarico = Azienda::findOrFail($id);
        return view('nomadelfia.incarichi.edit', compact('incarico'));

    }

    public function editConfirm(Request $request, $idPersona)
    {
    }

    public function insert(Request $request)
    {
        $validatedData = $request->validate([
            "name" => "required|unique:db_nomadelfia.aziende,nome_azienda",
        ], [
            'name.required' => "Il nome del'incarico aggiungere è obbligatorio.",
            'aziende.unique' => "L'incarico $request->name esistente già.",
        ]);

        Azienda::create(["nome_azienda" => $request->name, "tipo" => "incarico"]);
        return redirect()->back()->withSuccess("Incarico $request->name aggiunto correttamente.");
    }

    public function insertConfirm(Request $request)
    { //InsertClientiRequest $request

    }

    public function searchPersona(Request $request)
    {
        $term = $request->term;
        if ($term) {
            $persone = Persona::where("nominativo", "LIKE", "$term%")->orderBy("nominativo")->get();
        }

        if ($persone->count() > 0) {
            foreach ($persone as $persona) {
                $results[] = ['value' => $persona->id, 'label' => $persona->nominativo];
            }
            return response()->json($results);
        } else {
            return response()->json(['value' => "", 'label' => "persona non esiste"]);
        }

    }

}
