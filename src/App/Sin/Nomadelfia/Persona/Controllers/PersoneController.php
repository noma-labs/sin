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

<<<<<<< HEAD


=======
    public function search()
    {
        return view('nomadelfia.persone.search');
    }

    public function searchPersonaSubmit(Request $request)
    {
        /* $validatedData = $request->validate([
          'nominativo'=>"exists:db_biblioteca.editore,id",
          'nome'=>"exists:db_biblioteca.autore,id",
          'cognome'=>"exists:db_biblioteca.classificazione,id",
          ],[
            'xIdEditore.exists' => 'Editore inserito non esiste.',
            'xIdAutore.exists' => 'Autore inserito non esiste.',
            'xClassificazione.exists' => 'Classificazione inserita non esiste.',
        ]);
 */
        $msgSearch = ' ';
        $orderBy = 'nominativo';

        if (! $request->except(['_token'])) {
            return redirect()->route('nomadelfia.persone.ricerca')->withError('Nessun criterio di ricerca selezionato oppure invalido');
        }

        $queryLibri = Persona::sortable()->where(function ($q) use ($request, &$msgSearch, &$orderBy) {
            if ($request->nominativo) {
                $nominativo = $request->nominativo;
                $q->where('nominativo', 'like', "$nominativo%");
                $msgSearch = $msgSearch.'Nominativo='.$nominativo;
                $orderBy = 'nominativo';
            }
            if ($request->nome) {
                $nome = $request->nome;
                $q->where('nome', 'like', "$nome%");
                $msgSearch = $msgSearch.' Nome='.$nome;
                $orderBy = 'nominativo';
            }

            if ($request->filled('cognome')) {
                $cognome = $request->cognome;
                $q->where('cognome', 'like', "$cognome%");
                $msgSearch = $msgSearch.' Cognome='.$cognome;
                $orderBy = 'nome';
            }

            $criterio_nascita = $request->input('criterio_data_nascita', null);
            $nascita = $request->input('data_nascita', null);

            if ($criterio_nascita and $nascita) {
                $q->where('data_nascita', $criterio_nascita, $nascita);
                $msgSearch = $msgSearch.' Data Nascita'.$criterio_nascita.$nascita;
            }
        });
        $persone = $queryLibri->orderBy($orderBy)->paginate(50);

        return view('nomadelfia.persone.search_results', ['persone' => $persone, 'msgSearch' => $msgSearch]);
    }
>>>>>>> ca35960c4b0187938bf4f66725b596f6261e6da7
}
