<?php

declare(strict_types=1);

namespace App\Biblioteca\Controllers;

use App\Biblioteca\Models\Autore;
use Illuminate\Http\Request;

final class AuthorsController
{
    public function index(Request $request)
    {
        $term = $request->string('term');
        $query = Autore::query();
        if (! $term->isEmpty()) {
            $query->where('autore', 'like', "%$term%");
        }
        $autori = $query->orderBy('autore')->paginate(150);

        return view('biblioteca.authors.index')->with('autori', $autori);
    }

    public function create()
    {
        return view('biblioteca.authors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'autore' => ['required', 'unique:db_biblioteca.autore,autore'],
        ], [
            'autore.required' => "L'autore non può essere vuoto.",
            'autore.unique' => "L'autore $request->autore esistente già.",
        ]
        );
        $autore = new Autore;
        $autore->autore = $request->autore;
        $autore->save();

        return back()->withSuccess("Autore $autore->autore  aggiunto correttamente.");
    }

    public function show($id)
    {
        $autore = Autore::findOrFail($id);
        $books = $autore->libri()->orderBy('titolo');

        return view('biblioteca.authors.show', compact('autore', 'books'));
    }

    public function edit($id)
    {
        $autore = Autore::findOrFail($id);

        return view('biblioteca.authors.edit')->with('autore', $autore);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'autore' => 'required|unique:db_biblioteca.autore,autore,'.$id.',id',
        ], [
            'autore.required' => "L'autore non può essere vuoto.",
            'autore.unique' => "L'autore $request->autore esistente già.",
        ]
        );
        $autore = Autore::findOrFail($id);

        $autore->fill(['autore' => $request->autore]);
        if ($autore->save()) {
            return to_route('authors.index')->withSuccess('Autore '.$autore->autore.' aggiornato!');
        }

        return to_route('authors.index')->withErrors("Errore durante l'operaizone di aggiornamento");
    }

    public function destroy()
    {
        return to_route('authors.index')->withErrors("Impossibile eliminare l'autore");
    }
}
