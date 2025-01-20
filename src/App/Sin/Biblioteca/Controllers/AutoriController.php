<?php

declare(strict_types=1);

namespace App\Biblioteca\Controllers;

use App\Biblioteca\Models\Autore as Autore;
use Illuminate\Http\Request;

final class AutoriController
{
    public function index()
    {
        $autori = Autore::orderBy('Autore')->paginate(150);

        return view('biblioteca.autori.index')->with('autori', $autori);
    }

    public function create()
    {
        return view('biblioteca.autori.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'autore' => 'required|unique:db_biblioteca.autore,autore',
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

        return view('biblioteca.autori.show')->with('autore', $autore);
    }

    public function edit($id)
    {
        $autore = Autore::findOrFail($id);

        return view('biblioteca.autori.edit')->with('autore', $autore);
    }

    public function search(Request $request)
    {
        $request->validate([
            'idAutore' => 'required|exists:db_biblioteca.autore,id',
        ], [
            'idAutore.required' => 'L\'autore non può essere vuoto.',
            'idAutore.exists' => 'L\'autore non esiste.',
        ]
        );
        $autore = Autore::findOrFail($request->input('idAutore'));
        return view('biblioteca.autori.show')->with('autore', $autore);
    }

    public function update(Request $request, string $id)
    {
        // return $id;
        $autore = Autore::findOrFail($id); //Get role with the given id
        $request->validate([
            'autore' => 'required|unique:db_biblioteca.autore,autore,'.$id.',id',
        ], [
            'autore.required' => "L'autore non può essere vuoto.",
            'autore.unique' => "L'autore $request->autore esistente già.",
        ]
        );

        $autore->fill(['autore' => $request->autore]);
        if ($autore->save()) {
            return redirect()->route('autori.index')->withSuccess('Autore '.$autore->autore.' aggiornato!');
        }

        return redirect()->route('autori.index')->withErroe("Errore durante l'operaizone di aggiornamento");

    }

    public function destroy()
    {
        return redirect()->route('autori.index')->withError("Impossibile eliminare l'autore");
    }
}
