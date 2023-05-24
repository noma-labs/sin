<?php

namespace App\Nomadelfia\Persona\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;

class PersoneController extends CoreBaseController
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
     * Contolla che non ci sia una persona con il nome e cognome.
     * Ritorna la lista delle persone che hanno o il nome o cognome inserito.
     * Se non esistono persone ritorna il form per aggiungere la persona.
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
            } else {
                return redirect(route('nomadelfia.persone.anagrafica.create'))->withSuccess('Nessuna persona presente con nome e cognome inseriti.')->withInput();
            }
        }
    }

    public function delete($idPersona)
    {
        $persona = Persona::findOrFail($idPersona);
        if ($persona->delete()) {
            return redirect()->route('nomadelfia')->withSuccess("Persona $persona->nominativo eliminata con successo");
        }

        return view('nomadelfia')->withError("Impossibile eliminare $persona->nominativo ");
    }

}
