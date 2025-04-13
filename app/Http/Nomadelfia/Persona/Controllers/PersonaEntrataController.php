<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Controllers;

use App\Nomadelfia\Famiglia\Models\Famiglia;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataDallaNascitaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneAccoltoAction;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneConFamigliaAction;
use App\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use App\Nomadelfia\PopolazioneNomadelfia\Requests\EntrataPersonaRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

final class PersonaEntrataController
{
    public function create($idPersona)
    {
        $persona = Persona::findOrFail($idPersona);

        return view('nomadelfia.persone.popolazione.create', compact('persona'));
    }

    public function store(EntrataPersonaRequest $request, $idPersona)
    {
        $request->validated();

        $persona = Persona::findOrFail($idPersona);
        $data_entrata = Carbon::parse($request->input('data_entrata'));

        switch ($request->tipologia) {
            case 'dalla_nascita':
                $famiglia = Famiglia::findOrFail($request->input('famiglia_id'));
                $action = app(EntrataDallaNascitaAction::class);
                $action->execute($persona, $famiglia);
                break;
            case 'minorenne_accolto':
                $famiglia = Famiglia::findOrFail($request->input('famiglia_id'));
                $action = app(EntrataMinorenneAccoltoAction::class);
                $action->execute($persona, $data_entrata, $famiglia);
                break;
            case 'minorenne_famiglia':
                $famiglia = Famiglia::findOrFail($request->input('famiglia_id'));
                $action = app(EntrataMinorenneConFamigliaAction::class);
                $action->execute($persona, $data_entrata, $famiglia);
                break;
            case 'maggiorenne_single':
                $gruppoFamiliare = GruppoFamiliare::findOrFail($request->input('gruppo_id'));
                $action = app(EntrataMaggiorenneSingleAction::class);
                $action->execute($persona, $data_entrata, $gruppoFamiliare);
                break;
            case 'maggiorenne_famiglia':
                $gruppoFamiliare = GruppoFamiliare::findOrFail($request->input('gruppo_id'));
                $act = app(EntrataMaggiorenneConFamigliaAction::class);
                $act->execute($persona, $data_entrata, $gruppoFamiliare);
                break;
            default:
                return redirect()->back()->withErrore("Tipologia di entrata per $request->tipologia non riconosciuta.");

        }

        return redirect()->route('nomadelfia.persone.dettaglio',
            [$persona->id])->withSuccess('Persona '.$persona->nominativo.'inserita correttamente.');
    }

    public function update(Request $request, $idPersona, $entrata)
    {
        $request->validate([
            'data_entrata' => 'date',
        ], [
            'data_entrata.date' => 'La data entrata non è valida.',
        ]);
        $persona = Persona::findOrFail($idPersona);
        PopolazioneNomadelfia::query()
            ->where('persona_id', $persona->id)
            ->where('data_entrata', $entrata)
            ->update(['data_entrata' => $request->data_entrata]);

        return redirect()->back()->withSuccess("Data entrata di $persona->nominativo modificata con successo.");
    }
}
