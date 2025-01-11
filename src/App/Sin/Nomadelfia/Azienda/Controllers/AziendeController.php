<?php

declare(strict_types=1);

namespace App\Nomadelfia\Azienda\Controllers;

use Carbon\Carbon;
use Domain\Nomadelfia\Azienda\Models\Azienda;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\AssegnaAziendaAction;
use Illuminate\Http\Request;

final class AziendeController
{
    /**
     * view della pagina di gestione delle aziende
     *
     * @author Matteo Neri
     **/
    public function view()
    {
        $aziende = Azienda::aziende()->orderBy('nome_azienda')->with('lavoratoriAttuali')->get();

        return view('nomadelfia.aziende.index', compact('aziende'));
    }

    /**
     * ritorna la view per editare una azienda
     *
     * @author Matteo Neri
     **/
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

    public function assegnaPersona(Request $request, $id)
    {
        $request->validate([
            'persona_id' => 'required',
        ], [
            'persona_id.required' => 'La persona è obbligatoria.',
        ]);

        $persona = Persona::findOrFail($request->input('persona_id'));
        $azienda = Azienda::findOrFail($id);
        $action = new AssegnaAziendaAction;
        $action->execute($persona, $azienda, Carbon::parse($request->input('data')), Azienda::MANSIONE_LAVORATORE);

        return redirect()->back()->withSuccess("Persona $persona->nominativo  aggiunto a {$azienda->nome_azienda} con successo.");
    }

    public function spostaPersona(Request $request, $id, $idPersona)
    {
        $request->validate([
            'nuova_azienda_id' => 'required',
        ], [
            'nuova_azienda_id.required' => 'La nuova azienda è obbligatoria.',
        ]);

        $persona = Persona::findOrFail($idPersona);
        $azienda = Azienda::findOrFail($id);
        $nuova_azienda = Azienda::findOrFail($request->input('nuova_azienda_id'));

        $azienda->lavoratori()->updateExistingPivot($persona->id, ['stato' => 'Non Attivo', 'data_fine_azienda' => $request->input('data_fine')]);
        $nuova_azienda->lavoratori()->attach($persona->id, ['data_inizio_azienda' => $request->input('data_fine')]);

        return redirect()->back()->withSuccess("Persona $persona->nominativo  aggiunto a {$nuova_azienda->nome_azienda} con successo.");
    }
}
