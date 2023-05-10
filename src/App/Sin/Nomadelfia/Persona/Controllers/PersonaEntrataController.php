<?php

namespace App\Nomadelfia\Persona\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use App\Nomadelfia\PopolazioneNomadelfia\Requests\EntrataPersonaRequest;
use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataDallaNascitaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneConFamigliaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneAccoltoAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMinorenneConFamigliaAction;
use Illuminate\Http\Request;

class PersonaEntrataController extends CoreBaseController
{
    public function create($idPersona)
    {
        $persona = Persona::findOrFail($idPersona);
        return view('nomadelfia.persone.inserimento.entrata', compact('persona'));
    }

    public function store(EntrataPersonaRequest $request, $idPersona)
    {
        $request->validated();

        $persona = Persona::findOrFail($idPersona);
        $data_entrata = $request->input('data_entrata');
        if ($request->exists('gruppo_id')) {
            $gruppoFamiliare = GruppoFamiliare::findOrFail($request->input('gruppo_id'));
        }
        if ($request->exists('famiglia_id')) {
            $famiglia = Famiglia::findOrFail($request->input('famiglia_id'));
        }

        switch ($request->tipologia) {
            case 'dalla_nascita':
                $action = app(EntrataDallaNascitaAction::class);
                $action->execute($persona, $famiglia);
                break;
            case 'minorenne_accolto':
                $action = app(EntrataMinorenneAccoltoAction::class);
                $action->execute($persona, $data_entrata, $famiglia);
                break;
            case 'minorenne_famiglia':
                $action = app(EntrataMinorenneConFamigliaAction::class);
                $action->execute($persona, $data_entrata, $famiglia);
                break;
            case 'maggiorenne_single':
                $action = app(EntrataMaggiorenneSingleAction::class);
                $action->execute($persona, $data_entrata, $gruppoFamiliare);
                break;
            case 'maggiorenne_famiglia':
                $act = app(EntrataMaggiorenneConFamigliaAction::class);
                $act->execute($persona, $data_entrata, $gruppoFamiliare);
                break;
            default:
                return redirect()->back()->withErrore("Tipologia di entrata per $request->tipologia non riconosciuta.");

        }

        return redirect()->route('nomadelfia.persone.dettaglio',
            [$persona->id])->withSuccess('Persona ' . $persona->nominativo . 'inserita correttamente.');
    }

}
