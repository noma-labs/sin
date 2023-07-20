<?php

namespace App\Nomadelfia\Persona\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\UscitaPersonaAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use Illuminate\Http\Request;

class PersonaUscitaController extends CoreBaseController
{
    public function create($idPersona){
        $persona = Persona::findOrFail($idPersona);
        $first = $persona->getInitialLetterOfCogonome();
        $assegnati = Persona::NumeroElencoPrefixByLetter($first)->get();
        $propose = $persona->getOrCreateNumeroElenco();
        return view('nomadelfia.persone.popolazione.uscita', compact('persona', 'first', 'assegnati', 'propose'));
    }

    public function store(Request $request, $idPersona)
    {
        $validatedData = $request->validate([
            'data_uscita' => 'required',
            'numero_elenco' => 'required',
        ], [
            'data_uscita.required' => 'La data di uscita è obbligatoria',
            'numero_elenco.required' => 'Il numero di elenco è obbligatorio',
        ]);
        $persona = Persona::findOrFail($idPersona);
        if ($persona->isMoglie() or $persona->isCapofamiglia()) {
            return redirect()->back()->withError("La persona $persona->nominativo non può uscire da Nomadelfia perchè risulta essere moglie o capo famiglia. Far uscire tutta la famiglia dalla pagina di gestione famiglia.");
        }
        $act = app(UscitaPersonaAction::class);
        $act->execute($persona, $request->data_uscita);

        return redirect()->route('nomadelfia.persone.dettaglio',
            ['idPersona' => $idPersona])->withSuccess("La data di uscita di $persona->nominativo aggiornata correttamente.");

    }

    public function update(Request $request, $idPersona, $uscita)
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
}
