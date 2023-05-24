<?php

namespace App\Nomadelfia\Persona\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use Carbon\Carbon;
use Domain\Nomadelfia\Azienda\Models\Azienda;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
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

    public function updateDataEntrataNomadelfia(Request $request, $idPersona, $entrata)
    {
        $validatedData = $request->validate([
            'data_entrata' => 'date',
        ], [
            'data_entrata.date' => 'La data entrata non è valida.',
        ]);
        $persona = Persona::findOrFail($idPersona);
        PopolazioneNomadelfia::query()->where('persona_id', $persona->id)->where('data_entrata',
            $entrata)->update(['data_entrata' => $request->data_entrata]);

        return redirect()->back()->withSuccess("Data entrata di $persona->nominativo modificata con successo.");
    }

    public function updateDataUscitaNomadelfia(Request $request, $idPersona, $uscita)
    {
        $validatedData = $request->validate([
            'data_uscita' => 'date',
        ], [
            'data_uscita.date' => 'La data uscita non è valida.',
        ]);
        $persona = Persona::findOrFail($idPersona);
        PopolazioneNomadelfia::query()->where('persona_id', $persona->id)->where('data_uscita',
            $uscita)->update(['data_uscita' => $request->data_uscita]);

        return redirect()->back()->withSuccess("Data uscita di $persona->nominativo modificata con successo.");
    }

    /**
     * Ritorna la view per la modifica dello stato assegnato ad una persona
     *
     * @author Davide Neri
     */
    public function stato($idPersona)
    {
        $persona = Persona::findOrFail($idPersona);

        return view('nomadelfia.persone.stato.show', compact('persona'));
    }

    /**
     * Assegna un nuovo stato ad una persona.
     *
     * @author Davide Neri
     */
    public function assegnaStato(Request $request, $idPersona)
    {
        $validatedData = $request->validate([
            'stato_id' => 'required',
            'data_inizio' => 'required|date',
        ], [
            'stato_id.required' => 'Lo stato è obbligatorio',
            'data_inizio.required' => 'La data iniziale dello stato è obbligatoria.',
        ]);
        $persona = Persona::findOrFail($idPersona);
        $persona->assegnaStato($request->stato_id, $request->data_inizio, $request->data_fine);

        return redirect()->back()->withSuccess("Stato assegnato a $persona->nominativo con successo");
    }

    public function modificaStato(Request $request, $idPersona, $id)
    {
        $validatedData = $request->validate([
            'data_fine' => 'date',
            'data_inizio' => 'required|date',
            'stato' => 'required',
        ], [
            'data_fine.date' => 'La data fine posizione dee essere una data valida',
            'data_inizio.required' => 'La data di inizio della posizione è obbligatoria.',
            'stato.required' => 'Lo stato attuale è obbligatorio.',
        ]);
        $persona = Persona::findOrFail($idPersona);
        $persona->stati()->updateExistingPivot($id,
            ['data_fine' => $request->data_fine, 'data_inizio' => $request->data_inizio, 'stato' => $request->stato]);

        return redirect()->back()->withSuccess("Stato di  $persona->nominativo  modificato con successo.");
    }

    public function incarichi(Request $request, $idPersona)
    {
        $persona = Persona::findOrFail($idPersona);
        $attuali = $persona->incarichiAttuali();
        $storico = $persona->incarichiStorico();

        return view('nomadelfia.persone.incarichi.show', compact('persona', 'attuali', 'storico'));
    }

    public function assegnaIncarico(Request $request, $idPersona)
    {
        $validatedData = $request->validate([
            'azienda_id' => 'required',
            'mansione' => 'required',
            'data_inizio' => 'required|date',
        ], [
            'azienda_id.required' => "L'azienda è obbligatoria",
            'data_inizio.required' => "La data di inizio dell'azienda è obbligatoria.",
            'mansione.required' => "La mansione del lavoratore nell'azienda è obbligatoria.",

        ]);
        $persona = Persona::findOrFail($idPersona);
        $azienda = Azienda::incarichi()->findOrFail($request->azienda_id);
        if (strcasecmp($request->mansione, 'lavoratore') == 0) {
            $persona->assegnaLavoratoreIncarico($azienda, Carbon::parse($request->data_inizio));

            return redirect()->back()->withSuccess("$persona->nominativo assegnato incarico $azienda->nome_azienda come $request->mansione con successo");
        }
        if (strcasecmp($request->mansione, 'responsabile azienda') == 0) {
            $persona->assegnaResponsabileIncarico($azienda, $request->data_inizio);

            return redirect()->back()->withSuccess("$persona->nominativo assegnato incarico $azienda->nome_azienda come $request->mansione con successo");
        }

        return redirect()->back()->withError("La mansione $request->mansione non riconosciuta.");
    }

    public function modificaIncarico(Request $request, $idPersona, $id)
    {
        $validatedData = $request->validate([
            'mansione' => 'required',
            'data_entrata' => 'required|date',
            'stato' => 'required',
        ], [
            'data_entrata.required' => "La data di inizio dell'azienda è obbligatoria.",
            'mansione.required' => "La mansione del lavoratore nell'azienda è obbligatoria.",
            'stato.required' => 'Lo stato è obbligatoria.',
        ]);
        $persona = Persona::findOrFail($idPersona);
        $incarico = Azienda::incarichi()->findOrFail($id);
        $persona->incarichi()->updateExistingPivot($incarico->id, [
            'stato' => $request->stato,
            'data_inizio_azienda' => $request->data_entrata,
            'data_fine_azienda' => $request->data_uscita,
            'mansione' => $request->mansione,
        ]);

        return redirect()->back()->withSuccess("Incarico $incarico->nome_azienda di $persona->nominativo  modificata con successo.");
    }
}
