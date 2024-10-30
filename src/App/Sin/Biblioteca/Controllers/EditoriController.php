<?php

namespace App\Biblioteca\Controllers;

use App\Biblioteca\Models\Editore as Editore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EditoriController
{
    public function index()
    {
        $editori = Editore::orderBy('Editore')->paginate(150); //Get all roles

        return view('biblioteca.editori.index')->with('editori', $editori);
    }

    public function create()
    {
        if (Session::has('insertLibroUrl')) { // contains the url of the detail of the utente
            Session::keep('insertLibroUrl');
        }

        return view('biblioteca.editori.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'editore' => 'required|unique:db_biblioteca.editore,Editore',
        ], [
            'editore.required' => "L'editore non può essere vuoto.",
            'editore.unique' => "L'editore $request->editore esistente già.",
        ]
        );
        $editore = new Editore;
        $editore->editore = $request->editore;
        $editore->save();

        if (Session::has('insertLibroUrl')) {
            return redirect()->to(Session::get('insertLibroUrl'))->withSuccess("Editore $editore->editore  aggiunto correttamente.");
        } else {
            return redirect()->route('editori.index')->withSuccess("Editore $editore->editore  aggiunto correttamente.");
        }
    }

    public function search(Request $request)
    {
        if ($request->has('idEditore')) {
            $editore = Editore::findOrFail($request->input('idEditore'));

            return redirect()->route('editori.show', ['editori' => $editore->id]);
        }
    }

    public function show($id)
    {
        $editore = Editore::findOrFail($id);

        return view('biblioteca.editori.show')->with('editore', $editore);
    }

    public function edit($id)
    {
        $editore = Editore::findOrFail($id);

        return view('biblioteca.editori.edit')->with('editore', $editore);

    }

    public function update(Request $request, string $id)
    {
        $editore = Editore::findOrFail($id);

        $request->validate([
            'editore' => 'required|unique:db_biblioteca.editore,editore,'.$id.',id',
            // 'autore'=>'required|unique:db_biblioteca.autore,Autore,'.$id.",ID_AUTORE",
        ], [
            'editore.required' => "L'editore non può essere vuoto.",
            'editore.unique' => "L'editore $request->editore esistente già.",
        ]
        );

        $editore->fill(['editore' => $request->editore])->save();

        return redirect()->route('editori.index')->withSuccess('Editore '.$editore->editore.' aggiornato!');
    }

    public function destroy(Editore $editore)
    {
        return redirect()->route('editori.index')->withError("Impossibile eliminare l'editore");
    }
}
