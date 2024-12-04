<?php

declare(strict_types=1);

namespace App\Nomadelfia\GruppoFamiliare\Controllers;

use Carbon\Carbon;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\AssegnaGruppoFamiliareAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class PersonaGruppoFamiliareController
{
    public function index(Request $request, $idPersona)
    {
        $persona = Persona::findOrFail($idPersona);
        $attuale = $persona->gruppofamiliareAttuale();

        return view('nomadelfia.persone.gruppofamiliare.show', compact('persona', 'attuale'));
    }

    public function update(Request $request, $idPersona, $id)
    {
        $request->validate([
            'current_data_entrata' => 'required|date',
            'new_data_entrata' => 'required|date',
        ], [
            'current_data_entrata.date' => 'La data corrente di entrata non è una data valida',
            'current_data_entrata.required' => 'La data corrente di entrata dal gruppo è obbligatoria.',
            'new_data_entrata.required' => 'La data corrente di entrata dal gruppo è obbligatoria.',
            'new_data_entrata.date' => 'La data corrente di entrata non è una data valida',
        ]);
        $persona = Persona::findOrFail($idPersona);

        $expression = DB::raw('UPDATE gruppi_persone
               SET  data_entrata_gruppo = :new
               WHERE gruppo_famigliare_id  = :gruppo AND persona_id = :persona AND data_entrata_gruppo = :current');

        $res = DB::connection('db_nomadelfia')->update(
            $expression->getValue(DB::connection()->getQueryGrammar()),
            ['persona' => $idPersona, 'gruppo' => $id, 'current' => $request->current_data_entrata, 'new' => $request->new_data_entrata]
        );

        if ($res) {
            return redirect()
                ->action([self::class, 'index'], ['idPersona' => $persona->id])
                ->withSuccess("Gruppo familiare $persona->nominativo modificato con successo.");
        }

        return redirect()->back()->withError('Impossibile aggiornare la data di inizio del gruppo familiare.');
    }

    public function store(Request $request, $idPersona)
    {
        $request->validate([
            'gruppo_id' => 'required',
            'data_entrata' => 'required|date',
        ], [
            'gruppo_id.required' => 'Il nuovo gruppo è obbligatorio',
            'data_entrata.required' => 'La data di entrata nel gruppo familiare è obbligatoria.',
        ]);
        $persona = Persona::findOrFail($idPersona);
        $action = new AssegnaGruppoFamiliareAction;
        $action->execute($persona, GruppoFamiliare::findOrFail($request->gruppo_id), Carbon::parse($request->data_entrata));

        return redirect()
            ->action([self::class, 'index'], ['idPersona' => $persona->id])
            ->withSuccess("$persona->nominativo assegnato al gruppo familiare con successo");
    }

    public function delete($idPersona, $id)
    {
        $persona = Persona::findOrFail($idPersona);
        $res = $persona->gruppifamiliari()->detach($id);
        if ($res) {
            return redirect()
                ->action([self::class, 'index'], ['idPersona' => $persona->id])
                ->withSuccess("$persona->nominativo rimosso/a dal gruppo familiare con successo");
        }

        return redirect()->back()->withErro("Errore. Impossibile rimuovere $persona->nominativo dal gruppo familiare.");

    }
}
