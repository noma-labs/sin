<?php

declare(strict_types=1);

namespace App\Biblioteca\Controllers;

use App\Biblioteca\Models\Editore;
use Illuminate\Http\Request;

final class EditorsController
{
    public function index(Request $request)
    {
        $term = $request->string('term');
        $query = Editore::query();
        if (! $term->isEmpty()) {
            $query->where('editore', 'like', "%$term%");
        }
        $editori = $query->orderBy('Editore')->paginate(150);

        return view('biblioteca.editors.index')->with('editori', $editori);
    }

    public function create()
    {
        return view('biblioteca.editors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'editore' => ['required', 'unique:db_biblioteca.editore,Editore'],
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
        $request->validate([
            'editore' => 'required|unique:db_biblioteca.editore,editore,'.$id.',id',
        ], [
            'editore.required' => "L'editore non può essere vuoto.",
            'editore.unique' => "L'editore $request->editore esistente già.",
        ]);
        $editore = Editore::findOrFail($id);
        $editore->fill(['editore' => $request->editore])->save();

        return to_route('editors.index')->withSuccess('Editore '.$editore->editore.' aggiornato!');
    }

    public function destroy()
    {
        return to_route('editors.index')->withError("Impossibile eliminare l'editore");
    }
}
