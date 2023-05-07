<?php

namespace App\Nomadelfia\Persona\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use Carbon\Carbon;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;

class PersonaAnagraficaController extends CoreBaseController
{
    public function create()
    {
        return view('nomadelfia.persone.inserimento.dati_anagrafici');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nominativo' => 'required',
            'nome' => 'required',
            'cognome' => 'required',
            'data_nascita' => 'required|date',
            'luogo_nascita' => 'required',
            'sesso' => 'required',
        ], [
            'nominativo.required' => 'Il nominativo è obbligatorio',
            'nominativo.unique' => 'IL nominativo inserito esiste già.',
            'nome.required' => 'Il nome è obbligatorie',
            'cognome.required' => 'Il cognome è obbligatorio',
            'data_nascita.required' => 'La data di nascita è obbligatoria',
            'luogo_nascita.required' => 'IL luogo di nascita è obbligatorio',
            'sesso.required' => 'Il sesso della persona è obbligatorio',
        ]);
        //        $existing  = PopolazioneNomadelfia::presente()->where('nominativo', "", $request->input('nominativo'));
        //        if ($existing->count() > 0) {
        //            return redirect(route('nomadelfia.persone.inserimento'))->withError("Il nominativo inserito è già assegnato alla persona  $existing->nome $existing->cognome ($existing->data_nascita). Usare un altro nominativo.");
        //        }

        $persona = Persona::create(
            [
                'nominativo' => $request->input('nominativo'),
                'sesso' => $request->input('sesso'),
                'nome' => $request->input('nome'),
                'cognome' => $request->input('cognome'),
                'provincia_nascita' => $request->input('luogo_nascita'),
                'data_nascita' => $request->input('data_nascita'),
                'id_arch_pietro' => 0,
            ]
        );
        $res = $persona->save();
        if ($res) {
            return redirect(route('nomadelfia.persone.inserimento.entrata.scelta',
                ['idPersona' => $persona->id]))->withSuccess("Dati anagrafici di $persona->nominativo inseriti correttamente.");
        } else {
            return redirect(route('nomadelfia.persone.inserimento'))->withError("Errore. Persona $persona->nominativo non inserita.");
        }
    }
}
