<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Controllers;

use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;

final class PersoneController
{
    public function show($idPersona)
    {
        $persona = Persona::findOrFail($idPersona);
        $posizioneAttuale = $persona->posizioneAttuale();
        $gruppoAttuale = $persona->gruppofamiliareAttuale();
        $famigliaAttuale = $persona->famigliaAttuale();

        return view('nomadelfia.persone.show',
            compact('persona', 'posizioneAttuale', 'gruppoAttuale', 'famigliaAttuale'));
    }

    public function create()
    {
        return view('nomadelfia.persone.inserimento.initial');
    }

    /**
     * Check that there is no person with the given first and last name.
     *  Return the list of individuals who have either the entered first name or last name.
     *  If there are no matching individuals, return the form to add a new person.
     *
     * @author Davide Neri
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'persona' => 'required',
        ], [
            'persona.required' => 'Il cognome è obbligatorio',
        ]);

        if ($request->filled('persona')) {
            $personeEsistenti = Persona::where('nominativo', 'like', '%'.$request->persona.'%')
                ->orWhere('nome', 'like', '%'.$request->persona.'%')
                ->orWhere('cognome', 'like', '%'.$request->persona);
            if ($personeEsistenti->exists()) {
                return view('nomadelfia.persone.insert_existing', compact('personeEsistenti'));
            }

            return redirect(route('nomadelfia.persone.anagrafica.create'))->withSuccess('Nessuna persona presente con nome e cognome inseriti.')->withInput();

        }
    }

    public function delete($idPersona)
    {
        $persona = Persona::findOrFail($idPersona);
        if ($persona->delete()) {
            return redirect()->route('nomadelfia.index')->withSuccess("Persona $persona->nominativo eliminata con successo");
        }

        return view('nomadelfia')->withError("Impossibile eliminare $persona->nominativo ");
    }
}
