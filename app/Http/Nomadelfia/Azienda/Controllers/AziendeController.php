<?php

declare(strict_types=1);

namespace App\Nomadelfia\Azienda\Controllers;

use App\Nomadelfia\Azienda\Models\Azienda;
use App\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;

final class AziendeController
{
    public function view()
    {
        $aziende = Azienda::aziende()->orderBy('nome_azienda')->with('lavoratoriAttuali')->get();

        return view('nomadelfia.aziende.index', compact('aziende'));
    }

    public function edit($id)
    {
        $azienda = Azienda::with('lavoratoriAttuali')->with('lavoratoriStorici')->findOrFail($id);

        return view('nomadelfia.aziende.edit', compact('azienda'));

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
        }

        return response()->json(['value' => '', 'label' => 'persona non esiste']);

    }
}
