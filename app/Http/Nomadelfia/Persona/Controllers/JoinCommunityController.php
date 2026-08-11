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
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;

#[Middleware('auth')]
final readonly class JoinCommunityController
{
    public function __construct(
        private EntrataDallaNascitaAction $entrataDallaNascitaAction,
        private EntrataMinorenneAccoltoAction $entrataMinorenneAccoltoAction,
        private EntrataMinorenneConFamigliaAction $entrataMinorenneConFamigliaAction,
        private EntrataMaggiorenneSingleAction $entrataMaggiorenneSingleAction,
        private EntrataMaggiorenneConFamigliaAction $entrataMaggiorenneConFamigliaAction,
    ) {}

    #[Middleware('can:popolazione.persona.inserisci')]
    public function create($id)
    {
        $persona = Persona::findOrFail($id);

        return view('nomadelfia.persone.popolazione.create', compact('persona'));
    }

    #[Middleware('can:popolazione.persona.inserisci')]
    public function store(EntrataPersonaRequest $request, $id)
    {
        $request->validated();

        $persona = Persona::findOrFail($id);
        $data_entrata = \Illuminate\Support\Facades\Date::parse($request->input('data_entrata'));

        switch ($request->tipologia) {
            case 'dalla_nascita':
                $famiglia = Famiglia::findOrFail($request->input('famiglia_id'));
                $this->entrataDallaNascitaAction->execute($persona, $famiglia);
                break;
            case 'minorenne_accolto':
                $famiglia = Famiglia::findOrFail($request->input('famiglia_id'));
                $this->entrataMinorenneAccoltoAction->execute($persona, $data_entrata, $famiglia);
                break;
            case 'minorenne_famiglia':
                $famiglia = Famiglia::findOrFail($request->input('famiglia_id'));
                $this->entrataMinorenneConFamigliaAction->execute($persona, $data_entrata, $famiglia);
                break;
            case 'maggiorenne_single':
                $gruppoFamiliare = GruppoFamiliare::findOrFail($request->input('gruppo_id'));
                $this->entrataMaggiorenneSingleAction->execute($persona, $data_entrata, $gruppoFamiliare);
                break;
            case 'maggiorenne_famiglia':
                $gruppoFamiliare = GruppoFamiliare::findOrFail($request->input('gruppo_id'));
                $this->entrataMaggiorenneConFamigliaAction->execute($persona, $data_entrata, $gruppoFamiliare);
                break;
            default:
                return back()->withErrore("Tipologia di entrata per $request->tipologia non riconosciuta.");
        }

        return to_route('nomadelfia.person.show', $persona->id)->withSuccess('Persona '.$persona->nominativo.' inserita correttamente.');
    }

    #[Middleware('can:popolazione.persona.modifica')]
    public function update(Request $request, $id, $entrata)
    {
        $request->validate([
            'data_entrata' => ['date'],
        ], [
            'data_entrata.date' => 'La data entrata non è valida.',
        ]);
        $persona = Persona::findOrFail($id);
        PopolazioneNomadelfia::query()
            ->where('persona_id', $persona->id)
            ->where('data_entrata', $entrata)
            ->update(['data_entrata' => $request->data_entrata]);

        return back()->withSuccess("Data entrata di $persona->nominativo modificata con successo.");
    }
}
