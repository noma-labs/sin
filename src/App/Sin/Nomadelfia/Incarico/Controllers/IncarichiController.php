<?php

declare(strict_types=1);

namespace App\Nomadelfia\Incarico\Controllers;

use Carbon\Carbon;
use Domain\Nomadelfia\Incarico\Models\Incarico;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\AssegnaIncaricoAction;
use Illuminate\Http\Request;

final class IncarichiController
{
    public function view()
    {
        $incarichi = Incarico::all();
        $min = 4;
        $busy = Incarico::getBusyPeople($min);

        return view('nomadelfia.incarichi.index', compact('incarichi', 'busy', 'min'));
    }

    public function delete($id)
    {
        Incarico::destroy($id);

        return redirect()->back()->withSuccess('Incarico cancellato con successo.');
    }

    public function edit($id)
    {
        $incarico = Incarico::findOrFail($id);
        $lavoratori = $incarico->lavoratoriAttuali;
        $possibili = $incarico->lavoratoriPossibili();

        return view('nomadelfia.incarichi.edit', compact('incarico', 'lavoratori', 'possibili'));

    }

    public function assegnaPersona(Request $request, $id)
    {
        $request->validate([
            'persona_id' => 'required',
        ], [
            'persona_id.required' => 'La persona è obbligatoria.',
        ]);
        $incarico = Incarico::findOrFail($id);
        $persona = Persona::findOrFail($request->persona_id);
        $d = $request->input('data_inizio', Carbon::now()->toDateString());
        $action = new AssegnaIncaricoAction;
        $action->execute($persona, $incarico, Carbon::parse($d));

        return redirect()->back()->withSuccess("Persona $persona->nominativo  aggiunto a {$incarico->nome} con successo.");
    }

    public function eliminaPersona(Request $request, $id, $idPersona)
    {
        $incarico = Incarico::findOrFail($id);
        $incarico->lavoratori()->detach($idPersona);

        return redirect()->back()->withSuccess("Persona rimossa dall'incarico {$incarico->nome} con successo.");
    }

    public function insert(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:db_nomadelfia.incarichi,nome',
        ], [
            'name.required' => "Il nome dell'incarico  è obbligatorio.",
            'name.unique' => "L'incarico $request->name esiste già.",
        ]);

        Incarico::create(['nome' => $request->name]);

        return redirect()->back()->withSuccess("Incarico $request->name aggiunto correttamente.");
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
