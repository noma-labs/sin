<?php

declare(strict_types=1);

namespace App\Biblioteca\Controllers;

use App\Biblioteca\Models\Editore;
use Illuminate\Http\Request;

final class EditorsController
{
    public function index()
    {
        $editori = Editore::orderBy('Editore')->paginate(150);

        return view('biblioteca.editors.index')->with('editori', $editori);
    }

    public function create()
    {
        return view('biblioteca.editors.create');
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

        return back()->withSuccess("Editore $editore->editore  aggiunto correttamente.");
    }

    public function search(Request $request)
    {
        if ($request->filled('idEditore')) {
            $editore = Editore::findOrFail($request->input('idEditore'));

            return redirect()->route('editori.show', ['editori' => $editore->id]);
        }
    }

    public function show($id)
    {
        $editore = Editore::findOrFail($id);
        $books = $editore->libri()->orderBy('titolo');

        return view('biblioteca.editors.show', compact('editore', 'books'));
    }

    public function edit($id)
    {
        $editore = Editore::findOrFail($id);

        return view('biblioteca.editors.edit')->with('editore', $editore);

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

    public function destroy()
    {
        return redirect()->route('editori.index')->withError("Impossibile eliminare l'editore");
    }
}
