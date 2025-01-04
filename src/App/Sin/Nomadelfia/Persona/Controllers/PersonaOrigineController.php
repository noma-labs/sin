<?php

declare(strict_types=1);

namespace App\Nomadelfia\Persona\Controllers;

use App\Nomadelfia\PopolazioneNomadelfia\Requests\EntrataPersonaRequest;
use Carbon\Carbon;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataDallaNascitaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneAccoltoAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneConFamigliaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use Illuminate\Http\Request;

final class PersonaOrigineController
{
    public function create($idPersona)
    {
        $persona = Persona::findOrFail($idPersona);
        return view('nomadelfia.persone.inserimento.entrata-origine', compact('persona'));
    }

    public function store(Request $request, $idPersona)
    {
        $request->validate([
            'origine' => 'required',
        ], [
            'origine.required' => 'Origine è obbligatoria',
        ]);

        $persona = Persona::findOrFail($idPersona);
        dd($request->all());

        switch ($request->origine) {
            case 'interno':
                $persona->origine = "interno";
                break;
            case 'accolto':
                $famiglia = Famiglia::findOrFail($request->input('famiglia_id'));
                $action = app(EntrataMinorenneAccoltoAction::class);
                $action->execute($persona, $data_entrata, $famiglia);
                break;
            case 'minorenne_famiglia':
                $famiglia = Famiglia::findOrFail($request->input('famiglia_id'));
                $action = app(EntrataMinorenneConFamigliaAction::class);
                $action->execute($persona, $data_entrata, $famiglia);
                break;
            case 'esterno':
                $gruppoFamiliare = GruppoFamiliare::findOrFail($request->input('gruppo_id'));
                $act = app(EntrataMaggiorenneConFamigliaAction::class);
                $act->execute($persona, $data_entrata, $gruppoFamiliare);
                break;
            default:
                return redirect()->back()->withErrore("Tipologia di entrata per $request->origine non riconosciuta.");

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
